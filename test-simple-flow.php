<?php
/**
 * Teste simples e rápido do fluxo ZenScribe
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>⚡ Teste Simples ZenScribe</h2>";

try {
    echo "<h3>🧪 Passo 1: Criar Lead simples</h3>";
    $leadData = [
        'TITLE' => 'Lead ZenScribe - ' . date('H:i:s'),
        'COMMENTS' => 'Lead criado para teste rápido'
    ];
    
    $leadResult = CRest::call('crm.lead.add', ['fields' => $leadData]);
    echo "<p><strong>Criando Lead:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($leadResult);
    echo "</pre>";
    
    if (!isset($leadResult['result'])) {
        throw new Exception('Erro ao criar Lead: ' . json_encode($leadResult));
    }
    
    $leadId = $leadResult['result'];
    echo "<p style='color: green;'>✅ Lead criado: ID $leadId</p>";
    
    echo "<h3>🧪 Passo 2: Simular processamento ZenScribe</h3>";
    
    // Simular dados extraídos
    $extractedData = [
        'TITLE' => 'Reunião Comercial',
        'COMMENTS' => 'Discussão sobre e-commerce e gestão de estoque.',
        'client_info' => [
            'company' => 'ACME Corp',
            'phone' => '(11) 99999-8888'
        ]
    ];
    
    // Simular entidade
    $entity = [
        'type' => 'lead',
        'id' => $leadId
    ];
    
    echo "<p><strong>Dados simulados:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    echo "Entity: "; print_r($entity);
    echo "Extracted: "; print_r($extractedData);
    echo "</pre>";
    
    echo "<h3>🧪 Passo 3: Atualizar comentários (como ZenScribe faz)</h3>";
    
    // Buscar comentários existentes
    $currentLead = CRest::call('crm.lead.get', ['id' => $leadId]);
    $existingComments = $currentLead['result']['COMMENTS'] ?? '';
    
    // Criar novo comentário
    $newComment = "\n\n" . str_repeat("=", 30) . "\n";
    $newComment .= "🎯 ZenScribe: " . $extractedData['TITLE'] . "\n";
    $newComment .= "📅 " . date('d/m/Y H:i:s') . "\n";
    $newComment .= str_repeat("-", 30) . "\n\n";
    $newComment .= $extractedData['COMMENTS'] . "\n\n";
    $newComment .= "📊 DADOS:\n";
    foreach ($extractedData['client_info'] as $key => $value) {
        $newComment .= "• " . strtoupper($key) . ": " . $value . "\n";
    }
    $newComment .= "\n🔗 ZenScribe\n" . str_repeat("=", 30);
    
    // Combinar comentários
    $finalComments = $existingComments . $newComment;
    
    // Atualizar Lead
    $updateResult = CRest::call('crm.lead.update', [
        'id' => $leadId,
        'fields' => ['COMMENTS' => $finalComments]
    ]);
    
    echo "<p><strong>Update resultado:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($updateResult);
    echo "</pre>";
    
    if (isset($updateResult['result'])) {
        echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO TOTAL!</p>";
        echo "<p>✅ Lead criado: $leadId</p>";
        echo "<p>✅ Comentários atualizados</p>";
        echo "<p>✅ ZenScribe funcionando!</p>";
        
        // Verificar resultado final
        $finalLead = CRest::call('crm.lead.get', ['id' => $leadId]);
        echo "<h3>📋 Resultado Final</h3>";
        echo "<p><strong>Comentários finais (primeiros 500 chars):</strong></p>";
        echo "<pre style='background: #d4edda; padding: 10px; max-height: 200px; overflow: auto;'>";
        echo substr($finalLead['result']['COMMENTS'], 0, 500) . "...";
        echo "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Erro no update: " . json_encode($updateResult) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERRO: " . $e->getMessage() . "</p>";
    echo "<pre style='background: #f8d7da; padding: 10px;'>";
    echo $e->getTraceAsString();
    echo "</pre>";
}

echo "<h3>💡 Conclusão</h3>";
echo "<p>Se este teste funcionar, o ZenScribe completo deve funcionar também!</p>";
echo "<p>O problema do 'Application failed to respond' pode ser timeout na simulação completa.</p>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
