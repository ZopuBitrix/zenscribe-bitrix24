<?php
/**
 * Debug específico do update de comentários
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug do Update de Comentários</h2>";

echo "<h3>🧪 Teste 1: Criar Lead válido</h3>";
$testLead = [
    'TITLE' => 'Lead para Comments - ' . date('H:i:s'),
    'COMMENTS' => 'Comentário inicial do lead.'
];

$leadResult = CRest::call('crm.lead.add', ['fields' => $testLead]);
echo "<p><strong>Criando Lead:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($leadResult);
echo "</pre>";

if (isset($leadResult['result'])) {
    $leadId = $leadResult['result'];
    echo "<p style='color: green;'>✅ Lead criado: ID $leadId</p>";
    
    echo "<h3>🧪 Teste 2: Buscar comentários existentes</h3>";
    $getResult = CRest::call('crm.lead.get', ['id' => $leadId]);
    echo "<p><strong>crm.lead.get:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    if (isset($getResult['result']['COMMENTS'])) {
        echo "Comentários existentes: " . $getResult['result']['COMMENTS'] . "\n";
    } else {
        echo "Nenhum comentário encontrado\n";
    }
    echo "</pre>";
    
    echo "<h3>🧪 Teste 3: Update simples de comentários</h3>";
    $existingComments = $getResult['result']['COMMENTS'] ?? '';
    $newComment = "\n\n🎯 ZenScribe: Teste de atualização - " . date('H:i:s');
    $finalComments = $existingComments . $newComment;
    
    $updateParams = [
        'id' => $leadId,
        'fields' => [
            'COMMENTS' => $finalComments
        ]
    ];
    
    echo "<p><strong>Atualizando com:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    print_r($updateParams);
    echo "</pre>";
    
    $updateResult = CRest::call('crm.lead.update', $updateParams);
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($updateResult);
    echo "</pre>";
    
    if (isset($updateResult['result'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ SUCESSO! Comments atualizados!</p>";
        
        echo "<h3>🧪 Teste 4: Verificar resultado final</h3>";
        $finalCheck = CRest::call('crm.lead.get', ['id' => $leadId]);
        echo "<p><strong>Comentários finais:</strong></p>";
        echo "<pre style='background: #d4edda; padding: 10px;'>";
        echo $finalCheck['result']['COMMENTS'] ?? 'Nenhum comentário';
        echo "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Update falhou</p>";
        
        echo "<h3>🧪 Teste 5: Tentar apenas TITLE (teste se update funciona)</h3>";
        $titleUpdate = [
            'id' => $leadId,
            'fields' => [
                'TITLE' => 'Lead Atualizado - ' . date('H:i:s')
            ]
        ];
        
        $titleResult = CRest::call('crm.lead.update', $titleUpdate);
        echo "<p><strong>Update só TITLE:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px;'>";
        print_r($titleResult);
        echo "</pre>";
        
        if (isset($titleResult['result'])) {
            echo "<p>✅ TITLE update funciona - problema específico com COMMENTS</p>";
        } else {
            echo "<p>❌ Até TITLE update falha - problema no Lead ID</p>";
        }
    }
    
} else {
    echo "<p style='color: red;'>❌ Erro ao criar Lead</p>";
    
    echo "<h3>🧪 Teste Alternativo: Tentar Lead 67560 diretamente</h3>";
    $checkLead = CRest::call('crm.lead.get', ['id' => 67560]);
    echo "<p><strong>Lead 67560:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($checkLead);
    echo "</pre>";
    
    if (isset($checkLead['result'])) {
        echo "<p style='color: green;'>✅ Lead 67560 existe!</p>";
    } else {
        echo "<p style='color: red;'>❌ Lead 67560 não existe - por isso o erro!</p>";
    }
}

echo "<h3>📝 Diagnóstico</h3>";
echo "<p><strong>Possíveis causas do HTTP 400:</strong></p>";
echo "<ul>";
echo "<li>❌ Lead ID inválido (67560 não existe)</li>";
echo "<li>❌ Campo COMMENTS muito longo</li>";
echo "<li>❌ Caracteres especiais no texto</li>";
echo "<li>❌ Permissão de escrita no campo COMMENTS</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
