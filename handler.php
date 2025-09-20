<?php
/**
 * ZenScribe App - Processing Handler
 * Processa reuniões e atualiza Bitrix24
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

// Headers para CORS e JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Parse input JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

zenLog("Handler chamado", 'info', ['action' => $action, 'method' => $_SERVER['REQUEST_METHOD']]);

switch ($action) {
    case 'process_latest_meeting':
        processLatestMeeting();
        break;
    case 'process_specific_meeting':
        processSpecificMeeting($input);
        break;
    case 'test_google_auth':
        testGoogleAuth();
        break;
    case 'test_openai':
        testOpenAI();
        break;
    default:
        zenError('Action não reconhecida', ['action' => $action], 400);
}

/**
 * Processa a última reunião do Google Calendar
 */
function processLatestMeeting() {
    try {
        zenLog('Iniciando processamento da última reunião', 'info');
        
        // 1. Buscar última reunião
        $meeting = getLatestGoogleMeeting();
        if (!$meeting) {
            zenError('Nenhuma reunião encontrada nos últimos 7 dias');
            return;
        }
        
        zenLog('Reunião encontrada', 'info', ['title' => $meeting['title']]);
        
        // 2. Buscar transcrição
        $transcript = findMeetingTranscript($meeting);
        if (!$transcript) {
            zenError('Transcrição não encontrada para esta reunião');
            return;
        }
        
        zenLog('Transcrição encontrada', 'info', ['length' => strlen($transcript)]);
        
        // 3. Detectar entidade Bitrix24
        $entity = detectBitrixEntity($meeting['description']);
        if (!$entity) {
            // Padrão: criar novo lead sempre (sem ID específico)
            $entity = ['type' => 'lead', 'id' => null];
        }
        
        zenLog('Entidade detectada', 'info', $entity);
        
        // 4. Processar transcrição
        $extractedData = processTranscript($transcript);
        
        // 5. Atualizar/criar no Bitrix24
        $bitrixResult = updateBitrixEntity($entity, $extractedData, $meeting, $transcript);
        
        // 6. Adicionar comentários ricos (usa ID real do Lead criado)
        zenLog('Debug bitrixResult', 'debug', $bitrixResult);
        
        if (isset($bitrixResult['record_id'])) {
            $entity['id'] = $bitrixResult['record_id'];
            zenLog('Usando record_id do bitrixResult', 'info', ['entity_id' => $entity['id']]);
        } else {
            zenLog('record_id não encontrado, mantendo entity original', 'warning', ['entity' => $entity]);
        }
        
        try {
            $activityResult = createRichActivity($entity, $extractedData, $meeting, $transcript);
            zenLog('createRichActivity sucesso', 'info', $activityResult);
        } catch (Exception $e) {
            zenLog('createRichActivity falhou', 'error', ['error' => $e->getMessage()]);
            $activityResult = ['success' => false, 'error' => $e->getMessage()];
        }
        
        // 7. Pular agendamento para otimização (evitar timeout)
        $schedulingResult = ['scheduled' => false, 'reason' => 'optimization_disabled'];
        
        zenLog('Processamento concluído', 'info', [
            'entity' => $entity,
            'bitrix_result' => $bitrixResult,
            'activity_result' => $activityResult,
            'scheduling_result' => $schedulingResult
        ]);
        
        zenSuccess([
            'entity' => $entity,
            'extracted_data' => $extractedData,
            'bitrix_updated' => $bitrixResult,
            'activity_created' => $activityResult,
            'next_meeting' => $schedulingResult
        ], 'Reunião processada com sucesso!');
        
    } catch (Exception $e) {
        zenError('Erro no processamento: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }
}

/**
 * Busca última reunião do Google Calendar
 */
function getLatestGoogleMeeting() {
    $config = getZenScribeConfig();
    
    if (empty($config['google']['client_id'])) {
        throw new Exception('Google OAuth não configurado');
    }
    
    // Simular busca por enquanto - implementar OAuth completo depois
    $mockMeeting = [
        'id' => 'mock-meeting-' . time(),
        'title' => 'Reunião Comercial - Teste ZenScribe',
        'description' => 'Discussão sobre proposta de e-commerce e gestão de estoque.',
        'start' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'end' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        'attendees' => ['contato@cliente.com', 'vendedor@empresa.com']
    ];
    
    zenLog('Mock meeting retornado (implementar Google Calendar API)', 'debug', $mockMeeting);
    return $mockMeeting;
}

/**
 * Busca transcrição da reunião
 */
function findMeetingTranscript($meeting) {
    // Por enquanto retorna transcript mock - implementar Google Drive API depois
    $mockTranscript = "João: Boa tarde, hoje vamos discutir nossa proposta.
Cliente: Precisamos de uma solução para nosso e-commerce, temos problemas com gestão de estoque.
João: Entendi, qual o orçamento disponível?
Cliente: Temos uns R$ 75.000 para investir nesse projeto urgente.
João: Perfeito, nossa empresa ACME Soluções pode resolver isso. Vamos agendar próxima reunião semana que vem?
Cliente: Combinado! Nosso CNPJ é 12.345.678/0001-90, telefone (11) 99999-8888.";
    
    zenLog('Mock transcript retornado (implementar Google Drive API)', 'debug');
    return $mockTranscript;
}

/**
 * Detecta entidade Bitrix24 na descrição
 */
function detectBitrixEntity($description) {
    if (empty($description)) return null;
    
    // Busca URLs do Bitrix24
    if (preg_match('/bitrix24\.com(?:\.br)?\/crm\/(lead|deal|contact|company)\/details\/(\d+)/', $description, $matches)) {
        return [
            'type' => $matches[1],
            'id' => intval($matches[2])
        ];
    }
    
    // Busca padrões alternativos
    if (preg_match('/(lead|deal|contact|company)\s*#?(\d+)/i', $description, $matches)) {
        return [
            'type' => strtolower($matches[1]),
            'id' => intval($matches[2])
        ];
    }
    
    return null;
}

/**
 * Processa transcrição com IA ou heurísticas
 */
function processTranscript($transcript) {
    $config = getZenScribeConfig();
    
    if (!empty($config['openai']['api_key']) && $config['openai']['enabled']) {
        return processWithOpenAI($transcript, $config['openai']);
    } else {
        return processWithHeuristics($transcript);
    }
}

/**
 * Processamento com OpenAI
 */
function processWithOpenAI($transcript, $openaiConfig) {
    zenLog('Processando com OpenAI', 'info');
    
    $prompt = "Analise esta transcrição de reunião comercial e extraia dados estruturados:

TRANSCRIÇÃO:
$transcript

Extraia em formato JSON:
{
  \"TITLE\": \"Nome da empresa/cliente principal\",
  \"OPPORTUNITY\": \"Valor monetário (apenas números)\",
  \"COMMENTS\": \"Resumo executivo da reunião\",
  \"client_info\": {
    \"company\": \"Nome da empresa\",
    \"cnpj\": \"CNPJ se mencionado\",
    \"phone\": \"Telefone se mencionado\",
    \"email\": \"Email se mencionado\"
  },
  \"pain_points\": [\"dor1\", \"dor2\"],
  \"next_steps\": [\"ação1\", \"ação2\"],
  \"urgency\": \"alta/média/baixa\"
}";

    $data = [
        'model' => $openaiConfig['model'],
        'messages' => [
            ['role' => 'system', 'content' => 'Você é um especialista em análise de reuniões comerciais. Responda apenas com JSON válido.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 1000,
        'temperature' => 0.3
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $openaiConfig['api_key'],
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        zenLog('OpenAI falhou, usando heurísticas', 'warn', ['http_code' => $httpCode]);
        return processWithHeuristics($transcript);
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['choices'][0]['message']['content'])) {
        zenLog('Resposta OpenAI inválida, usando heurísticas', 'warn');
        return processWithHeuristics($transcript);
    }
    
    $content = $result['choices'][0]['message']['content'];
    $extractedData = json_decode(trim($content, '```json'), true);
    
    if (!$extractedData) {
        zenLog('JSON OpenAI inválido, usando heurísticas', 'warn');
        return processWithHeuristics($transcript);
    }
    
    zenLog('Dados extraídos com OpenAI', 'info', $extractedData);
    return $extractedData;
}

/**
 * Processamento com heurísticas
 */
function processWithHeuristics($transcript) {
    zenLog('Processando com heurísticas', 'info');
    
    $data = [];
    
    // Extrair empresa/cliente
    if (preg_match('/(?:empresa|companhia)\s+(?:é\s+)?([A-Z][^,.\n]{3,30})/i', $transcript, $matches)) {
        $data['TITLE'] = trim($matches[1]);
    } else {
        $data['TITLE'] = 'Cliente processado via ZenScribe';
    }
    
    // Extrair valor monetário
    if (preg_match('/R\$\s*(\d{1,3}(?:\.\d{3})*(?:,\d{2})?)/i', $transcript, $matches)) {
        $data['OPPORTUNITY'] = str_replace(['.', ','], ['', '.'], $matches[1]);
    }
    
    // Extrair CNPJ
    $clientInfo = [];
    if (preg_match('/(?:CNPJ|cnpj)[:\s]*(\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2})/', $transcript, $matches)) {
        $clientInfo['cnpj'] = $matches[1];
    }
    
    // Extrair telefone
    if (preg_match('/(?:telefone|phone|fone)[:\s]*([0-9\s\(\)\-\+]{8,})/i', $transcript, $matches)) {
        $clientInfo['phone'] = trim($matches[1]);
    }
    
    // Extrair email
    if (preg_match('/(?:email|e-mail)[:\s]*([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i', $transcript, $matches)) {
        $clientInfo['email'] = $matches[1];
    }
    
    if (!empty($clientInfo)) {
        $data['client_info'] = $clientInfo;
    }
    
    // Extrair dores/problemas
    $painPoints = [];
    if (preg_match_all('/(?:problema|dor|dificuldade)[^.!?]*[.!?]/i', $transcript, $matches)) {
        $painPoints = array_slice($matches[0], 0, 3);
    }
    $data['pain_points'] = $painPoints;
    
    // Extrair próximos passos
    $nextSteps = [];
    if (preg_match_all('/(?:próxim|agendar|vamos)[^.!?]*[.!?]/i', $transcript, $matches)) {
        $nextSteps = array_slice($matches[0], 0, 3);
    }
    $data['next_steps'] = $nextSteps;
    
    // Detectar urgência
    $urgencyWords = ['urgente', 'rápido', 'imediato', 'hoje', 'amanhã'];
    $urgency = 'baixa';
    foreach ($urgencyWords as $word) {
        if (stripos($transcript, $word) !== false) {
            $urgency = 'alta';
            break;
        }
    }
    $data['urgency'] = $urgency;
    
    // Gerar comentários estruturados
    $comments = "📋 RESUMO DA REUNIÃO:\n\n";
    
    if (!empty($data['pain_points'])) {
        $comments .= "😰 PROBLEMAS IDENTIFICADOS:\n";
        foreach ($data['pain_points'] as $i => $pain) {
            $comments .= ($i + 1) . ". " . trim($pain) . "\n";
        }
        $comments .= "\n";
    }
    
    if (!empty($data['next_steps'])) {
        $comments .= "🎯 PRÓXIMOS PASSOS:\n";
        foreach ($data['next_steps'] as $i => $step) {
            $comments .= ($i + 1) . ". " . trim($step) . "\n";
        }
        $comments .= "\n";
    }
    
    if (!empty($clientInfo)) {
        $comments .= "📞 DADOS DE CONTATO:\n";
        if (isset($clientInfo['cnpj'])) $comments .= "• CNPJ: " . $clientInfo['cnpj'] . "\n";
        if (isset($clientInfo['phone'])) $comments .= "• Telefone: " . $clientInfo['phone'] . "\n";
        if (isset($clientInfo['email'])) $comments .= "• Email: " . $clientInfo['email'] . "\n";
        $comments .= "\n";
    }
    
    if (!empty($data['OPPORTUNITY'])) {
        $comments .= "💰 ORÇAMENTO MENCIONADO: R$ " . number_format($data['OPPORTUNITY'], 2, ',', '.') . "\n\n";
    }
    
    $comments .= "📝 TRANSCRIÇÃO:\n" . substr($transcript, 0, 300) . "...";
    
    $data['COMMENTS'] = $comments;
    
    zenLog('Dados extraídos com heurísticas', 'info', $data);
    return $data;
}

/**
 * Atualiza entidade no Bitrix24
 */
function updateBitrixEntity($entity, $extractedData, $meeting, $transcript) {
    try {
        $fields = [
            'TITLE' => $extractedData['TITLE'] ?? 'Reunião processada via ZenScribe',
            'COMMENTS' => $extractedData['COMMENTS'] ?? substr($transcript, 0, 500)
        ];
        
        if (isset($extractedData['OPPORTUNITY'])) {
            $fields['OPPORTUNITY'] = $extractedData['OPPORTUNITY'];
        }
        
        // Adicionar campos customizados se disponíveis
        if (isset($extractedData['client_info'])) {
            // Mapear para campos customizados do Bitrix24
            // Implementar mapeamento específico aqui
        }
        
        if ($entity['id']) {
            // Verificar se a entidade realmente existe antes de tentar atualizar
            $checkMethod = 'crm.' . $entity['type'] . '.get';
            $checkResult = CRest::call($checkMethod, ['id' => $entity['id']]);
            
            if (isset($checkResult['error']) || empty($checkResult['result'])) {
                // Entidade não existe, criar nova
                zenLog('Entidade não existe, criando nova', 'info', [
                    'entity_id' => $entity['id'],
                    'entity_type' => $entity['type']
                ]);
                $method = 'crm.' . $entity['type'] . '.add';
                $params = [
                    'fields' => $fields
                ];
            } else {
                // Entidade existe, atualizar
                zenLog('Entidade existe, atualizando', 'info', [
                    'entity_id' => $entity['id'],
                    'entity_type' => $entity['type']
                ]);
                $method = 'crm.' . $entity['type'] . '.update';
                $params = [
                    'id' => $entity['id'],
                    'fields' => $fields
                ];
            }
        } else {
            // Criar novo (ID não informado)
            $method = 'crm.' . $entity['type'] . '.add';
            $params = [
                'fields' => $fields
            ];
        }
        
        // Debug: Logar chamada antes de executar
        zenLog('Tentando chamada Bitrix24', 'debug', [
            'method' => $method,
            'params' => $params,
            'entity' => $entity
        ]);
        
        $result = CRest::call($method, $params);
        
        // Debug: Logar resultado
        zenLog('Resultado chamada Bitrix24', 'debug', $result);
        
        if (isset($result['error'])) {
            // Incluir mais detalhes no erro
            $errorMsg = 'Erro Bitrix24: ' . ($result['error_description'] ?? $result['error']);
            $errorMsg .= ' | Method: ' . $method;
            $errorMsg .= ' | Params: ' . json_encode($params);
            throw new Exception($errorMsg);
        }
        
        $recordId = $result['result'] ?? $entity['id'];
        
        zenLog('Entidade Bitrix24 atualizada', 'info', [
            'method' => $method,
            'record_id' => $recordId,
            'fields' => $fields
        ]);
        
        return [
            'success' => true,
            'method' => $method,
            'record_id' => $recordId,
            'fields' => $fields
        ];
        
    } catch (Exception $e) {
        zenLog('Erro ao atualizar Bitrix24', 'error', ['error' => $e->getMessage()]);
        throw $e;
    }
}

/**
 * Adiciona informações da reunião no campo COMMENTS da entidade
 */
function createRichActivity($entity, $extractedData, $meeting, $transcript) {
    try {
        // Construir comentário rico para timeline
        $timelineComment = "🎯 ZenScribe: " . ($extractedData['TITLE'] ?? 'Reunião processada') . "\n";
        $timelineComment .= "📅 " . date('d/m/Y H:i:s') . "\n";
        $timelineComment .= str_repeat("-", 50) . "\n\n";
        
        // Adicionar resumo
        $timelineComment .= $extractedData['COMMENTS'] ?? substr($transcript, 0, 500);
        
        // Adicionar dados estruturados se disponíveis
        if (isset($extractedData['client_info'])) {
            $timelineComment .= "\n\n📊 DADOS EXTRAÍDOS:\n";
            foreach ($extractedData['client_info'] as $key => $value) {
                if (!empty($value)) {
                    $timelineComment .= "• " . strtoupper($key) . ": " . $value . "\n";
                }
            }
        }
        
        $timelineComment .= "\n🔗 Processado automaticamente pelo ZenScribe";
        
        // Usar crm.timeline.comment.add ao invés de tentar atualizar COMMENTS
        $commentParams = [
            'ENTITY_ID' => $entity['id'],
            'ENTITY_TYPE' => $entity['type'], // 'lead', 'deal', etc.
            'COMMENT' => $timelineComment
        ];
        
        zenLog('Adicionando comentário no timeline', 'info', [
            'entity_type' => $entity['type'],
            'entity_id' => $entity['id'],
            'comment_size' => strlen($timelineComment)
        ]);
        
        $result = CRest::call('crm.timeline.comment.add', $commentParams);
        
        if (!$result || isset($result['error'])) {
            $error = isset($result['error']) ? $result['error']['error_description'] : 'Erro desconhecido';
            zenLog('Erro ao adicionar timeline comment', 'error', ['error' => $error, 'params' => $commentParams]);
            throw new Exception('Erro Timeline: ' . $error);
        }
        
        $commentId = $result['result'];
        zenLog('Timeline comment adicionado com sucesso', 'success', [
            'entity_id' => $entity['id'],
            'entity_type' => $entity['type'],
            'comment_id' => $commentId
        ]);
        
        return [
            'success' => true,
            'entity_id' => $entity['id'],
            'comment_id' => $commentId,
            'timeline_comment_added' => true
        ];
        
    } catch (Exception $e) {
        zenLog('Erro ao atualizar comentários', 'error', ['error' => $e->getMessage()]);
        throw $e;
    }
}

/**
 * Agenda próxima reunião se necessário
 */
function scheduleNextMeeting($transcript, $meeting, $extractedData) {
    // Detectar se precisa agendar
    if (!preg_match('/(?:próxim|agendar|reunião|encontro)/i', $transcript)) {
        return ['scheduled' => false, 'reason' => 'no_follow_up_needed'];
    }
    
    // Detectar urgência/prazo
    $days = 7; // padrão: 1 semana
    if (stripos($transcript, 'urgente') !== false || stripos($transcript, 'esta semana') !== false) {
        $days = 3;
    } elseif (stripos($transcript, 'mês') !== false) {
        $days = 30;
    }
    
    // Por enquanto apenas simular - implementar Google Calendar API depois
    $nextDate = date('Y-m-d H:i:s', strtotime("+$days days"));
    
    zenLog('Agendamento simulado', 'info', [
        'next_date' => $nextDate,
        'days_ahead' => $days
    ]);
    
    return [
        'scheduled' => true,
        'date' => $nextDate,
        'days_ahead' => $days,
        'title' => 'Follow-up: ' . ($extractedData['TITLE'] ?? 'Reunião'),
        'simulated' => true
    ];
}

/**
 * Testa autenticação Google (simplificado como Avoma)
 */
function testGoogleAuth() {
    $config = getZenScribeConfig();
    
    if (empty($config['google']['client_id']) || empty($config['google']['client_secret'])) {
        zenError('Google Client ID ou Secret não configurado');
        return;
    }
    
    // Teste simples de validação da API Key (similar à Avoma)
    try {
        // Apenas verificar se as credenciais tem formato válido
        $clientId = $config['google']['client_id'];
        $clientSecret = $config['google']['client_secret'];
        
        if (!preg_match('/^\d+-[a-zA-Z0-9]+\.apps\.googleusercontent\.com$/', $clientId)) {
            zenError('Google Client ID com formato inválido');
            return;
        }
        
        if (strlen($clientSecret) < 10) {
            zenError('Google Client Secret muito curto');
            return;
        }
        
        zenSuccess([
            'google_configured' => true,
            'client_id' => substr($clientId, 0, 20) . '...'
        ], 'Google OAuth configurado e validado');
        
    } catch (Exception $e) {
        zenError('Erro ao validar Google: ' . $e->getMessage());
    }
}

/**
 * Testa OpenAI (simplificado como Avoma)
 */
function testOpenAI() {
    $config = getZenScribeConfig();
    
    if (empty($config['openai']['api_key'])) {
        zenError('OpenAI API Key não configurado');
        return;
    }
    
    $apiKey = $config['openai']['api_key'];
    
    // Validação de formato (como Avoma faz)
    if (!preg_match('/^sk-proj-[a-zA-Z0-9\-_]{20,}$/', $apiKey) && !preg_match('/^sk-[a-zA-Z0-9]{20,}$/', $apiKey)) {
        zenError('OpenAI API Key com formato inválido');
        return;
    }
    
    // Teste real da API (direto como Avoma)
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            zenError('Erro de conexão OpenAI: ' . $error);
            return;
        }
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $models = $data['data'] ?? [];
            
            zenSuccess([
                'openai_working' => true,
                'models_count' => count($models),
                'api_key_prefix' => substr($apiKey, 0, 10) . '...'
            ], 'OpenAI API funcionando perfeitamente');
        } else {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? 'Erro desconhecido';
            zenError('OpenAI API falhou: ' . $errorMsg, ['http_code' => $httpCode]);
        }
        
    } catch (Exception $e) {
        zenError('Erro ao testar OpenAI: ' . $e->getMessage());
    }
}
?>
