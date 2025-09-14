<?php
/**
 * ZenScribe - Teste de Simulação
 * Testa o fluxo completo com dados simulados
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'run_simulation';

switch ($action) {
    case 'run_simulation':
        runFullSimulation();
        break;
    case 'test_openai_only':
        testOpenAIExtraction();
        break;
    case 'test_bitrix_only':
        testBitrixUpdate();
        break;
    default:
        zenError('Ação não reconhecida', ['action' => $action], 400);
}

/**
 * Executa simulação completa do fluxo
 */
function runFullSimulation() {
    try {
        zenLog('🧪 Iniciando simulação completa', 'info');
        
        // 1. Dados simulados da reunião
        $meeting = [
            'title' => 'Reunião Comercial - Proposta E-commerce',
            'description' => 'Reunião com cliente sobre projeto de e-commerce. Lead: https://zopu.bitrix24.com.br/crm/lead/details/67890',
            'start_time' => date('c', strtotime('-1 hour')),
            'end_time' => date('c'),
            'participants' => ['joao@vendedor.com', 'cliente@empresa.com.br']
        ];
        
        // 2. Transcrição simulada (comercial)
        $transcript = "João: Boa tarde! Obrigado por aceitar nossa reunião hoje.

Cliente: Oi João, obrigado você. Estamos muito interessados no projeto de e-commerce.

João: Perfeito! Me conte um pouco sobre as principais dificuldades que vocês estão enfrentando hoje.

Cliente: Olha, nossa maior dor é que nosso site atual é muito lento, os clientes reclamam constantemente. Além disso, não temos integração com nosso estoque, então vendemos produtos que não temos. É muito frustrante.

João: Entendo perfeitamente. E qual seria o orçamento que vocês têm em mente para resolver isso?

Cliente: Estamos pensando em investir entre R$ 80.000 a R$ 120.000 para ter uma solução completa.

João: Ótimo! E quais são os seus principais desejos para a nova plataforma?

Cliente: Queremos um site super rápido, integração total com estoque, sistema de pagamento moderno, painel administrativo intuitivo e que seja responsivo no mobile.

João: Perfeito! Baseado no que vocês me falaram, eu vou preparar uma proposta detalhada. Acredito que podemos entregar tudo isso em aproximadamente 120 horas de desenvolvimento.

Cliente: Quanto tempo seria isso?

João: Cerca de 3 meses de trabalho. E me diga, o que vocês NÃO querem que esteja incluído no escopo?

Cliente: Não queremos marketing digital, não queremos app mobile por enquanto, e não precisamos de integração com redes sociais.

João: Perfeito! Vou agendar uma próxima reunião para apresentar a proposta. Que tal semana que vem?

Cliente: Perfeito, João! Aguardo ansiosamente.";
        
        // 3. Detectar entidade Bitrix24
        $entity = detectBitrixEntity($meeting['description']);
        if (!$entity) {
            $entity = ['type' => 'lead', 'id' => 67890]; // Entidade simulada
        }
        
        zenLog('📊 Entidade detectada', 'info', $entity);
        
        // 4. Processar transcrição com OpenAI
        $extractedData = processTranscriptSimulation($transcript);
        
        // 5. Simular atualização Bitrix24
        $bitrixResult = simulateBitrixUpdate($entity, $extractedData, $meeting, $transcript);
        
        // 6. Simular criação de atividade
        $activityResult = simulateRichActivity($entity, $extractedData, $meeting, $transcript);
        
        // 7. Agendamento automático
        $schedulingResult = simulateScheduling($transcript, $meeting, $extractedData);
        
        zenLog('✅ Simulação completa concluída', 'info');
        
        zenSuccess([
            'simulation' => 'complete',
            'steps' => [
                'meeting_data' => $meeting,
                'entity_detected' => $entity,
                'extracted_data' => $extractedData,
                'bitrix_result' => $bitrixResult,
                'activity_result' => $activityResult,
                'scheduling_result' => $schedulingResult
            ],
            'summary' => [
                'entity_type' => $entity['type'],
                'entity_id' => $entity['id'],
                'openai_used' => !empty($extractedData['ai_processed']),
                'scheduling_detected' => $schedulingResult['scheduled']
            ]
        ], 'Simulação completa executada com sucesso!');
        
    } catch (Exception $e) {
        zenError('Erro na simulação: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }
}

/**
 * Processa transcrição (versão simulada que chama OpenAI real)
 */
function processTranscriptSimulation($transcript) {
    $config = getZenScribeConfig();
    
    // Se OpenAI está habilitado, usar IA real
    if (!empty($config['openai']['api_key']) && $config['openai']['enabled']) {
        return extractDataWithOpenAI($transcript);
    }
    
    // Senão, usar dados heurísticos simulados
    return [
        'ai_processed' => false,
        'method' => 'heuristic_simulation',
        'TITLE' => 'Reunião Comercial - Proposta E-commerce',
        'OPPORTUNITY' => '100000',
        'COMMENTS' => 'DORES IDENTIFICADAS:\n• Site lento com reclamações de clientes\n• Falta de integração com estoque\n• Vendas de produtos indisponíveis\n\nDESEJOS DO CLIENTE:\n• Site super rápido\n• Integração total com estoque\n• Sistema de pagamento moderno\n• Painel administrativo intuitivo\n• Design responsivo mobile\n\nESTIMATIVA: 120 horas de desenvolvimento (3 meses)\n\nESCOPO NEGATIVO:\n• Marketing digital\n• App mobile\n• Integração redes sociais',
        'urgency' => 'alta',
        'next_steps' => ['Preparar proposta detalhada', 'Agendar reunião de apresentação'],
        'pain_points' => [
            'Site atual muito lento',
            'Falta integração com estoque',
            'Vendas sem controle de disponibilidade',
            'Experiência do cliente prejudicada'
        ],
        'desires' => [
            'Performance superior',
            'Integração completa',
            'Pagamentos modernos',
            'Interface intuitiva',
            'Mobile responsivo'
        ]
    ];
}

/**
 * Simula atualização no Bitrix24
 */
function simulateBitrixUpdate($entity, $extractedData, $meeting, $transcript) {
    // Em modo simulação, apenas logar o que seria feito
    $fields = [
        'TITLE' => $extractedData['TITLE'] ?? $meeting['title'],
        'OPPORTUNITY' => $extractedData['OPPORTUNITY'] ?? '',
        'COMMENTS' => $extractedData['COMMENTS'] ?? substr($transcript, 0, 500) . '...'
    ];
    
    zenLog('🔄 Simulando atualização Bitrix24', 'info', [
        'entity_type' => $entity['type'],
        'entity_id' => $entity['id'],
        'fields' => $fields
    ]);
    
    return [
        'simulated' => true,
        'would_update' => $entity['type'] . '.update',
        'entity_id' => $entity['id'],
        'fields' => $fields,
        'success' => true
    ];
}

/**
 * Simula criação de atividade rica
 */
function simulateRichActivity($entity, $extractedData, $meeting, $transcript) {
    $activity = [
        'OWNER_TYPE_ID' => ($entity['type'] === 'lead') ? 1 : 2,
        'OWNER_ID' => $entity['id'],
        'TYPE_ID' => 6,
        'SUBJECT' => '🎯 ZenScribe: ' . ($extractedData['TITLE'] ?? $meeting['title']),
        'DESCRIPTION' => $extractedData['COMMENTS'] ?? $transcript,
        'COMPLETED' => 'Y',
        'RESPONSIBLE_ID' => 1
    ];
    
    zenLog('📝 Simulando atividade rica', 'info', $activity);
    
    return [
        'simulated' => true,
        'would_create' => 'crm.activity.add',
        'activity_data' => $activity,
        'success' => true
    ];
}

/**
 * Simula agendamento automático
 */
function simulateScheduling($transcript, $meeting, $extractedData) {
    // Detectar se precisa agendar
    if (!preg_match('/(?:próxim|agendar|reunião|encontro|semana que vem)/i', $transcript)) {
        return ['scheduled' => false, 'reason' => 'no_follow_up_detected'];
    }
    
    $nextDate = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    zenLog('📅 Simulando agendamento', 'info', [
        'next_date' => $nextDate,
        'title' => 'Follow-up: Apresentação de Proposta'
    ]);
    
    return [
        'scheduled' => true,
        'simulated' => true,
        'date' => $nextDate,
        'title' => 'Follow-up: Apresentação de Proposta',
        'would_create' => 'Google Calendar Event'
    ];
}

/**
 * Testa apenas extração OpenAI
 */
function testOpenAIExtraction() {
    $transcript = "Cliente: Nossa empresa precisa urgentemente de um novo sistema. Nossos custos estão altos demais e a produtividade está baixa. Vendedor: Entendo. Qual o orçamento disponível? Cliente: Temos R$ 50.000 para investir.";
    
    $result = extractDataWithOpenAI($transcript);
    
    zenSuccess($result, 'Teste OpenAI concluído');
}

/**
 * Testa apenas integração Bitrix24
 */
function testBitrixUpdate() {
    $entity = ['type' => 'lead', 'id' => 12345];
    $data = [
        'TITLE' => 'Teste ZenScribe',
        'COMMENTS' => 'Teste de integração automática'
    ];
    
    $result = simulateBitrixUpdate($entity, $data, [], '');
    
    zenSuccess($result, 'Teste Bitrix24 concluído');
}

/**
 * Extrai dados usando OpenAI (função real)
 */
function extractDataWithOpenAI($transcript) {
    $config = getZenScribeConfig();
    
    if (empty($config['openai']['api_key'])) {
        throw new Exception('OpenAI não configurado');
    }
    
    $prompt = "Analise esta transcrição de reunião comercial e extraia:

1. DORES (4 principais dores do cliente, máximo 280 caracteres cada)
2. DESEJOS (5 principais desejos, título + 3 linhas de 50 caracteres)
3. ESTIMATIVA (tabela em horas baseada nas sugestões do vendedor)
4. ESCOPO NEGATIVO (o que NÃO está incluído)

Transcrição:
$transcript

Retorne em formato JSON estruturado.";
    
    $data = [
        'model' => $config['openai']['model'],
        'messages' => [
            ['role' => 'system', 'content' => 'Você é um especialista em análise de reuniões comerciais B2B.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 1500,
        'temperature' => 0.3
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['openai']['api_key'],
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('OpenAI API falhou: ' . $response);
    }
    
    $result = json_decode($response, true);
    $content = $result['choices'][0]['message']['content'] ?? '';
    
    // Tentar parsear JSON da resposta
    $extracted = json_decode($content, true);
    
    if (!$extracted) {
        // Se não for JSON válido, processar como texto
        $extracted = [
            'ai_processed' => true,
            'raw_response' => $content,
            'TITLE' => 'Reunião Comercial Processada',
            'COMMENTS' => $content
        ];
    } else {
        $extracted['ai_processed'] = true;
    }
    
    zenLog('🤖 OpenAI extração concluída', 'info', [
        'input_length' => strlen($transcript),
        'output_length' => strlen($content)
    ]);
    
    return $extracted;
}

/**
 * Detecta entidade Bitrix24 da descrição
 */
function detectBitrixEntity($description) {
    // Regex para URLs do Bitrix24
    $pattern = '/https?:\/\/[^\/]+\.bitrix24\.com\.br\/crm\/(lead|deal|contact|company)\/details\/(\d+)/i';
    
    if (preg_match($pattern, $description, $matches)) {
        return [
            'type' => strtolower($matches[1]),
            'id' => (int)$matches[2],
            'source' => 'url_detection'
        ];
    }
    
    // Padrões alternativos (Lead #123, ID: 456)
    if (preg_match('/(?:lead|negócio|contato)\s*#?(\d+)/i', $description, $matches)) {
        return [
            'type' => 'lead', // padrão
            'id' => (int)$matches[1],
            'source' => 'pattern_detection'
        ];
    }
    
    return null;
}
?>
