<?php
/**
 * Debug específico do campo COMMUNICATIONS
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug do campo COMMUNICATIONS</h2>";

echo "<h3>🧪 Teste 1: Atividade SEM COMMUNICATIONS (teste básico)</h3>";
$noComm = [
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste sem COMMUNICATIONS',
    'RESPONSIBLE_ID' => 7
];

$result1 = CRest::call('crm.activity.add', ['fields' => $noComm]);
echo "<p><strong>Sem COMMUNICATIONS:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result1);
echo "</pre>";

if (isset($result1['result'])) {
    echo "<p style='color: green;'>✅ Funcionou sem COMMUNICATIONS!</p>";
} else {
    echo "<p style='color: red;'>❌ Falha mesmo sem COMMUNICATIONS</p>";
}

echo "<h3>🧪 Teste 2: COMMUNICATIONS em formato diferente</h3>";
$commString = [
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste COMMUNICATIONS string',
    'RESPONSIBLE_ID' => 7,
    'COMMUNICATIONS' => 'teste@email.com'  // Como string
];

$result2 = CRest::call('crm.activity.add', ['fields' => $commString]);
echo "<p><strong>COMMUNICATIONS como string:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result2);
echo "</pre>";

echo "<h3>🧪 Teste 3: COMMUNICATIONS apenas com VALUE</h3>";
$commSimple = [
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste COMMUNICATIONS simples',
    'RESPONSIBLE_ID' => 7,
    'COMMUNICATIONS' => [
        [
            'VALUE' => 'teste@email.com'
        ]
    ]
];

$result3 = CRest::call('crm.activity.add', ['fields' => $commSimple]);
echo "<p><strong>COMMUNICATIONS só com VALUE:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result3);
echo "</pre>";

echo "<h3>🧪 Teste 4: COMMUNICATIONS com TYPE EMAIL</h3>";
$commEmail = [
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste COMMUNICATIONS EMAIL',
    'RESPONSIBLE_ID' => 7,
    'COMMUNICATIONS' => [
        [
            'TYPE' => 'EMAIL',
            'VALUE' => 'teste@zenscribe.com'
        ]
    ]
];

$result4 = CRest::call('crm.activity.add', ['fields' => $commEmail]);
echo "<p><strong>COMMUNICATIONS com EMAIL:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result4);
echo "</pre>";

if (isset($result4['result'])) {
    echo "<p style='color: green;'>✅ Funcionou com EMAIL!</p>";
} else {
    echo "<p style='color: red;'>❌ Falha com EMAIL</p>";
}

echo "<h3>🧪 Teste 5: COMMUNICATIONS com TYPE PHONE</h3>";
$commPhone = [
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste COMMUNICATIONS PHONE',
    'RESPONSIBLE_ID' => 7,
    'COMMUNICATIONS' => [
        [
            'TYPE' => 'PHONE',
            'VALUE' => '11999999999'
        ]
    ]
];

$result5 = CRest::call('crm.activity.add', ['fields' => $commPhone]);
echo "<p><strong>COMMUNICATIONS com PHONE:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result5);
echo "</pre>";

echo "<h3>🧪 Teste 6: Verificar campos de COMMUNICATIONS válidos</h3>";
$commFields = CRest::call('crm.activity.communication.fields');
echo "<p><strong>Campos de COMMUNICATIONS:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px; max-height: 300px; overflow: auto;'>";
print_r($commFields);
echo "</pre>";

echo "<h3>🧪 Teste 7: Atividade completamente manual (como Avoma faz)</h3>";
$manual = [
    'TYPE_ID' => 6,  // Task
    'SUBJECT' => 'Manual Test',
    'RESPONSIBLE_ID' => 7,
    'COMPLETED' => 'Y'
];

$result7 = CRest::call('crm.activity.add', ['fields' => $manual]);
echo "<p><strong>Atividade manual (sem OWNER, sem COMMUNICATIONS):</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result7);
echo "</pre>";

if (isset($result7['result'])) {
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO! Atividade manual criada: " . $result7['result'] . "</p>";
    echo "<p>Isso prova que o webhook funciona, o problema é na estrutura dos campos!</p>";
} else {
    echo "<p style='color: red;'>❌ Até atividade manual falha - problema no webhook</p>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
