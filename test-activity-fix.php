<?php
/**
 * Teste do crm.activity.add com campo COMMUNICATIONS obrigatório
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔧 Teste FIXADO - crm.activity.add com COMMUNICATIONS</h2>";

echo "<h3>🧪 Teste 1: Atividade com COMMUNICATIONS</h3>";
$activityWithComm = [
    'OWNER_TYPE_ID' => 1, // Lead
    'OWNER_ID' => 1,
    'TYPE_ID' => 2, // Meeting
    'SUBJECT' => 'Teste ZenScribe com COMMUNICATIONS',
    'DESCRIPTION' => 'Testando atividade com campo obrigatório COMMUNICATIONS',
    'COMPLETED' => 'Y',
    'RESPONSIBLE_ID' => 1,
    'COMMUNICATIONS' => [
        [
            'TYPE' => 'EMAIL',
            'VALUE' => 'teste@zenscribe.com'
        ]
    ]
];

echo "<p><strong>Campos a enviar (com COMMUNICATIONS):</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($activityWithComm);
echo "</pre>";

$result1 = CRest::call('crm.activity.add', ['fields' => $activityWithComm]);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result1);
echo "</pre>";

if (isset($result1['result'])) {
    echo "<p style='color: green;'>✅ Sucesso! Atividade criada com ID: " . $result1['result'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Ainda com erro</p>";
}

echo "<h3>🧪 Teste 2: COMMUNICATIONS vazio</h3>";
$activityEmptyComm = [
    'OWNER_TYPE_ID' => 1,
    'OWNER_ID' => 1,
    'TYPE_ID' => 2,
    'SUBJECT' => 'Teste COMMUNICATIONS vazio',
    'COMPLETED' => 'Y',
    'RESPONSIBLE_ID' => 1,
    'COMMUNICATIONS' => []
];

$result2 = CRest::call('crm.activity.add', ['fields' => $activityEmptyComm]);
echo "<p><strong>COMMUNICATIONS vazio:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result2);
echo "</pre>";

echo "<h3>🧪 Teste 3: Diferentes tipos de COMMUNICATIONS</h3>";
$commTypes = [
    'PHONE' => '11999999999',
    'EMAIL' => 'contato@empresa.com',
    'WEB' => 'https://empresa.com',
    'OTHER' => 'Google Meet'
];

foreach ($commTypes as $type => $value) {
    $testActivity = [
        'OWNER_TYPE_ID' => 1,
        'OWNER_ID' => 1,
        'TYPE_ID' => 2,
        'SUBJECT' => "Teste COMMUNICATIONS $type",
        'COMPLETED' => 'Y',
        'RESPONSIBLE_ID' => 1,
        'COMMUNICATIONS' => [
            [
                'TYPE' => $type,
                'VALUE' => $value
            ]
        ]
    ];
    
    $typeResult = CRest::call('crm.activity.add', ['fields' => $testActivity]);
    
    echo "<p><strong>$type:</strong> ";
    if (isset($typeResult['result'])) {
        echo "✅ OK (ID: {$typeResult['result']})</p>";
    } else {
        echo "❌ Falhou</p>";
        echo "<pre style='background: #ffe6e6; padding: 5px; font-size: 12px;'>";
        print_r($typeResult);
        echo "</pre>";
    }
}

echo "<h3>🧪 Teste 4: ZenScribe Real (como será usado)</h3>";
$zenscribeActivity = [
    'OWNER_TYPE_ID' => 1, // Lead
    'OWNER_ID' => 1,
    'TYPE_ID' => 2, // Meeting
    'SUBJECT' => '🎯 ZenScribe: Reunião Comercial Processada',
    'DESCRIPTION' => 'Reunião processada automaticamente pelo ZenScribe. Transcript disponível no Google Drive.',
    'COMPLETED' => 'Y',
    'RESPONSIBLE_ID' => 1,
    'PRIORITY' => '2',
    'COMMUNICATIONS' => [
        [
            'TYPE' => 'OTHER',
            'VALUE' => 'Google Meet - ZenScribe'
        ]
    ]
];

echo "<p><strong>Atividade ZenScribe final:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($zenscribeActivity);
echo "</pre>";

$finalResult = CRest::call('crm.activity.add', ['fields' => $zenscribeActivity]);
echo "<p><strong>Resultado final:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($finalResult);
echo "</pre>";

if (isset($finalResult['result'])) {
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO! ZenScribe atividade criada com ID: " . $finalResult['result'] . "</p>";
    echo "<p>Agora podemos atualizar o handler.php com o campo COMMUNICATIONS!</p>";
} else {
    echo "<p style='color: red;'>❌ Ainda precisa ajustar</p>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
