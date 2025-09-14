<?php
/**
 * Debug da atividade exata que está falhando
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug da atividade exata que falha</h2>";

echo "<h3>🧪 Teste 1: Verificar se Lead 67560 existe</h3>";
$leadCheck = CRest::call('crm.lead.get', ['id' => 67560]);
echo "<p><strong>Lead 67560:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($leadCheck);
echo "</pre>";

if (isset($leadCheck['result'])) {
    echo "<p style='color: green;'>✅ Lead 67560 existe!</p>";
} else {
    echo "<p style='color: red;'>❌ Lead 67560 não existe</p>";
}

echo "<h3>🧪 Teste 2: Atividade exata do erro</h3>";
$exactActivity = [
    'OWNER_TYPE_ID' => 1,
    'OWNER_ID' => 67560,
    'TYPE_ID' => 2,
    'SUBJECT' => '🎯 ZenScribe: Cliente',
    'DESCRIPTION' => 'Discussão sobre proposta para solução de e-commerce e gestão de estoque.',
    'COMPLETED' => 'Y',
    'RESPONSIBLE_ID' => 7,
    'PRIORITY' => '3',
    'COMMUNICATIONS' => [
        [
            'TYPE' => 'OTHER',
            'VALUE' => 'Google Meet - ZenScribe'
        ]
    ]
];

echo "<p><strong>Campos exatos do erro:</strong></p>";
echo "<pre style='background: #ffe6e6; padding: 10px;'>";
print_r($exactActivity);
echo "</pre>";

$exactResult = CRest::call('crm.activity.add', ['fields' => $exactActivity]);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($exactResult);
echo "</pre>";

echo "<h3>🧪 Teste 3: Remover PRIORITY</h3>";
$noPriority = $exactActivity;
unset($noPriority['PRIORITY']);

echo "<p><strong>Sem PRIORITY:</strong></p>";
$result3 = CRest::call('crm.activity.add', ['fields' => $noPriority]);
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result3);
echo "</pre>";

if (isset($result3['result'])) {
    echo "<p style='color: green;'>✅ Funcionou sem PRIORITY!</p>";
} else {
    echo "<p style='color: red;'>❌ Ainda falha sem PRIORITY</p>";
}

echo "<h3>🧪 Teste 4: PRIORITY como número</h3>";
$numPriority = $exactActivity;
$numPriority['PRIORITY'] = 3; // número ao invés de string

$result4 = CRest::call('crm.activity.add', ['fields' => $numPriority]);
echo "<p><strong>PRIORITY como número (3):</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result4);
echo "</pre>";

echo "<h3>🧪 Teste 5: PRIORITY = 2</h3>";
$priority2 = $exactActivity;
$priority2['PRIORITY'] = '2';

$result5 = CRest::call('crm.activity.add', ['fields' => $priority2]);
echo "<p><strong>PRIORITY = '2':</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result5);
echo "</pre>";

echo "<h3>🧪 Teste 6: PRIORITY = 1</h3>";
$priority1 = $exactActivity;
$priority1['PRIORITY'] = '1';

$result6 = CRest::call('crm.activity.add', ['fields' => $priority1]);
echo "<p><strong>PRIORITY = '1':</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result6);
echo "</pre>";

echo "<h3>🧪 Teste 7: Sem emoji no SUBJECT</h3>";
$noEmoji = $exactActivity;
$noEmoji['SUBJECT'] = 'ZenScribe: Cliente';

$result7 = CRest::call('crm.activity.add', ['fields' => $noEmoji]);
echo "<p><strong>Sem emoji:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result7);
echo "</pre>";

if (isset($result7['result'])) {
    echo "<p style='color: green;'>✅ Funcionou sem emoji!</p>";
} else {
    echo "<p style='color: red;'>❌ Ainda falha sem emoji</p>";
}

echo "<h3>🧪 Teste 8: Atividade super mínima</h3>";
$minimal = [
    'OWNER_TYPE_ID' => 1,
    'OWNER_ID' => 67560,
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste Minimal',
    'RESPONSIBLE_ID' => 7,
    'COMMUNICATIONS' => [
        [
            'TYPE' => 'OTHER',
            'VALUE' => 'Teste'
        ]
    ]
];

$result8 = CRest::call('crm.activity.add', ['fields' => $minimal]);
echo "<p><strong>Atividade mínima:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result8);
echo "</pre>";

if (isset($result8['result'])) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCESSO com atividade mínima! ID: " . $result8['result'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Até a mínima falha</p>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
