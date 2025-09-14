<?php
/**
 * Teste específico do crm.activity.add
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Teste do crm.activity.add</h2>";

echo "<h3>🧪 Teste 1: Verificar campos de atividade disponíveis</h3>";
$activityFields = CRest::call('crm.activity.fields');
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow: auto;'>";
if (isset($activityFields['result'])) {
    echo "Campos disponíveis:\n";
    foreach ($activityFields['result'] as $fieldName => $fieldInfo) {
        echo "- $fieldName: " . ($fieldInfo['title'] ?? 'N/A') . " (";
        echo "Tipo=" . ($fieldInfo['type'] ?? 'N/A');
        if (isset($fieldInfo['isRequired']) && $fieldInfo['isRequired']) {
            echo ", OBRIGATÓRIO";
        }
        echo ")\n";
    }
} else {
    print_r($activityFields);
}
echo "</pre>";

echo "<h3>🧪 Teste 2: Criar atividade simples</h3>";
$simpleActivity = [
    'OWNER_TYPE_ID' => 1, // Lead
    'OWNER_ID' => 1, // ID qualquer
    'TYPE_ID' => 6, // Task/Meeting
    'SUBJECT' => 'Teste ZenScribe - ' . date('H:i:s'),
    'DESCRIPTION' => 'Atividade de teste criada pelo ZenScribe',
    'COMPLETED' => 'Y',
    'RESPONSIBLE_ID' => 1
];

echo "<p><strong>Campos a enviar:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($simpleActivity);
echo "</pre>";

$result1 = CRest::call('crm.activity.add', ['fields' => $simpleActivity]);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result1);
echo "</pre>";

if (isset($result1['result'])) {
    echo "<p style='color: green;'>✅ Atividade simples criada! ID: " . $result1['result'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Erro na atividade simples</p>";
}

echo "<h3>🧪 Teste 3: Atividade com emojis (como ZenScribe)</h3>";
$emojiActivity = [
    'OWNER_TYPE_ID' => 1,
    'OWNER_ID' => 1,
    'TYPE_ID' => 6,
    'SUBJECT' => '🎯 ZenScribe: Reunião Comercial - Teste',
    'DESCRIPTION' => 'Atividade com emoji no título e descrição 📝',
    'COMPLETED' => 'Y',
    'RESPONSIBLE_ID' => 1,
    'PRIORITY' => '2'
];

echo "<p><strong>Campos com emoji:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($emojiActivity);
echo "</pre>";

$result2 = CRest::call('crm.activity.add', ['fields' => $emojiActivity]);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result2);
echo "</pre>";

if (isset($result2['result'])) {
    echo "<p style='color: green;'>✅ Atividade com emoji criada! ID: " . $result2['result'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Problema com emojis?</p>";
}

echo "<h3>🧪 Teste 4: Testar diferentes TYPE_ID</h3>";
$typeTests = [
    ['TYPE_ID' => 1, 'name' => 'Call'],
    ['TYPE_ID' => 2, 'name' => 'Meeting'],
    ['TYPE_ID' => 3, 'name' => 'Task'],
    ['TYPE_ID' => 6, 'name' => 'Other']
];

foreach ($typeTests as $typeTest) {
    $testActivity = [
        'OWNER_TYPE_ID' => 1,
        'OWNER_ID' => 1,
        'TYPE_ID' => $typeTest['TYPE_ID'],
        'SUBJECT' => 'Teste TYPE_ID ' . $typeTest['TYPE_ID'] . ' (' . $typeTest['name'] . ')',
        'COMPLETED' => 'Y',
        'RESPONSIBLE_ID' => 1
    ];
    
    $typeResult = CRest::call('crm.activity.add', ['fields' => $testActivity]);
    
    echo "<p><strong>TYPE_ID {$typeTest['TYPE_ID']} ({$typeTest['name']}):</strong> ";
    if (isset($typeResult['result'])) {
        echo "✅ OK (ID: {$typeResult['result']})</p>";
    } else {
        echo "❌ Falhou</p>";
        echo "<pre style='background: #f8f9fa; padding: 5px; font-size: 12px;'>";
        print_r($typeResult);
        echo "</pre>";
    }
}

echo "<h3>🧪 Teste 5: Atividade sem campos opcionais</h3>";
$minimalActivity = [
    'OWNER_TYPE_ID' => 1,
    'OWNER_ID' => 1,
    'TYPE_ID' => 2,
    'SUBJECT' => 'Atividade Mínima',
    'RESPONSIBLE_ID' => 1
];

$result5 = CRest::call('crm.activity.add', ['fields' => $minimalActivity]);
echo "<p><strong>Resultado (só campos obrigatórios):</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result5);
echo "</pre>";

echo "<hr>";
echo "<h3>📊 Diagnóstico:</h3>";
echo "<p>Com base nos testes:</p>";
echo "<ul>";
echo "<li>✅ Campos obrigatórios identificados</li>";
echo "<li>✅ Teste de emojis no SUBJECT</li>";
echo "<li>✅ Teste de diferentes TYPE_ID</li>";
echo "<li>✅ Teste de atividade mínima</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
