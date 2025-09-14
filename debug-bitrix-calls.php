<?php
/**
 * Debug das chamadas para Bitrix24
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug das Chamadas Bitrix24</h2>";

echo "<h3>🧪 Teste 1: user.current (básico)</h3>";
$result1 = CRest::call('user.current');
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
print_r($result1);
echo "</pre>";

echo "<hr><h3>🧪 Teste 2: crm.lead.list (listar leads)</h3>";
$result2 = CRest::call('crm.lead.list', ['filter' => ['ID' => 67890]]);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
print_r($result2);
echo "</pre>";

echo "<hr><h3>🧪 Teste 3: crm.lead.fields (campos disponíveis)</h3>";
$result3 = CRest::call('crm.lead.fields');
echo "<p><strong>Resultado (primeiros campos):</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow: auto;'>";
if (isset($result3['result'])) {
    $fields = array_slice($result3['result'], 0, 10, true); // Mostrar só os primeiros 10
    print_r($fields);
    echo "\n... (" . count($result3['result']) . " campos no total)";
} else {
    print_r($result3);
}
echo "</pre>";

echo "<hr><h3>🧪 Teste 4: Simular criação de lead</h3>";
$testFields = [
    'TITLE' => 'Teste ZenScribe - ' . date('Y-m-d H:i:s'),
    'COMMENTS' => 'Lead de teste criado pelo ZenScribe para verificar funcionamento'
];

echo "<p><strong>Campos a enviar:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($testFields);
echo "</pre>";

$result4 = CRest::call('crm.lead.add', ['fields' => $testFields]);
echo "<p><strong>Resultado criação:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
print_r($result4);
echo "</pre>";

if (isset($result4['result'])) {
    $newLeadId = $result4['result'];
    echo "<p style='color: green;'>✅ <strong>Lead criado com sucesso! ID: $newLeadId</strong></p>";
    
    echo "<h4>🧪 Teste 5: Atualizar lead criado</h4>";
    $updateFields = [
        'COMMENTS' => 'Lead atualizado pelo ZenScribe - ' . date('H:i:s')
    ];
    
    $result5 = CRest::call('crm.lead.update', ['id' => $newLeadId, 'fields' => $updateFields]);
    echo "<p><strong>Resultado atualização:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($result5);
    echo "</pre>";
    
    if (isset($result5['result']) && $result5['result'] === true) {
        echo "<p style='color: green;'>✅ <strong>Lead atualizado com sucesso!</strong></p>";
    }
}

echo "<hr><h3>📊 Resumo do Diagnóstico:</h3>";
echo "<ul>";
echo "<li><strong>user.current:</strong> " . (isset($result1['result']) ? '✅ OK' : '❌ Falhou') . "</li>";
echo "<li><strong>crm.lead.list:</strong> " . (isset($result2['result']) ? '✅ OK' : '❌ Falhou') . "</li>";
echo "<li><strong>crm.lead.fields:</strong> " . (isset($result3['result']) ? '✅ OK' : '❌ Falhou') . "</li>";
echo "<li><strong>crm.lead.add:</strong> " . (isset($result4['result']) ? '✅ OK' : '❌ Falhou') . "</li>";
if (isset($result4['result'])) {
    echo "<li><strong>crm.lead.update:</strong> " . (isset($result5['result']) ? '✅ OK' : '❌ Falhou') . "</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h3>🔧 Próximos passos:</h3>";
if (isset($result1['result']) && isset($result4['result'])) {
    echo "<p style='color: green;'>✅ <strong>Bitrix24 funcionando perfeitamente!</strong></p>";
    echo "<p>O erro HTTP 400 do ZenScribe deve estar em outro lugar. Vamos investigar o handler.</p>";
} else {
    echo "<p style='color: red;'>❌ <strong>Problema encontrado!</strong></p>";
    echo "<p>Veja os erros acima para identificar o problema específico.</p>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
