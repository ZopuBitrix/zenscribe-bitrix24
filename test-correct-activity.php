<?php
/**
 * Teste com TYPE_ID correto baseado nos tipos disponíveis
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🎯 Teste com TYPE_ID correto</h2>";

echo "<h3>🧪 Teste 1: TYPE_ID = 1 (Reunião)</h3>";
$meeting = [
    'TYPE_ID' => 1,
    'SUBJECT' => 'ZenScribe: Reunião de Teste',
    'RESPONSIBLE_ID' => 7
];

$result1 = CRest::call('crm.activity.add', ['fields' => $meeting]);
echo "<p><strong>TYPE_ID = 1 (Reunião):</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result1);
echo "</pre>";

if (isset($result1['result'])) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCESSO! Atividade criada: " . $result1['result'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Ainda falha</p>";
}

echo "<h3>🧪 Teste 2: TYPE_ID = 3 (Tarefa)</h3>";
$task = [
    'TYPE_ID' => 3,
    'SUBJECT' => 'ZenScribe: Tarefa de Teste',
    'RESPONSIBLE_ID' => 7
];

$result2 = CRest::call('crm.activity.add', ['fields' => $task]);
echo "<p><strong>TYPE_ID = 3 (Tarefa):</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result2);
echo "</pre>";

if (isset($result2['result'])) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCESSO! Tarefa criada: " . $result2['result'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ Ainda falha</p>";
}

echo "<h3>🧪 Teste 3: Usando atividade existente como modelo</h3>";
$existingActivity = CRest::call('crm.activity.get', ['id' => 17999]);
echo "<p><strong>Atividade existente (ID: 17999):</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px; max-height: 300px; overflow: auto;'>";
print_r($existingActivity);
echo "</pre>";

if (isset($existingActivity['result'])) {
    $template = $existingActivity['result'];
    
    echo "<h3>🧪 Teste 4: Copiar estrutura da atividade existente</h3>";
    $copyActivity = [
        'TYPE_ID' => $template['TYPE_ID'],
        'SUBJECT' => 'ZenScribe: Cópia de ' . $template['SUBJECT'],
        'RESPONSIBLE_ID' => 7,
    ];
    
    // Adicionar campos obrigatórios se existirem
    if (!empty($template['DIRECTION'])) {
        $copyActivity['DIRECTION'] = $template['DIRECTION'];
    }
    if (!empty($template['PRIORITY'])) {
        $copyActivity['PRIORITY'] = $template['PRIORITY'];
    }
    
    echo "<p><strong>Copiando estrutura:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    print_r($copyActivity);
    echo "</pre>";
    
    $result4 = CRest::call('crm.activity.add', ['fields' => $copyActivity]);
    echo "<p><strong>Resultado da cópia:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($result4);
    echo "</pre>";
    
    if (isset($result4['result'])) {
        echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO TOTAL! Atividade criada: " . $result4['result'] . "</p>";
        
        echo "<h3>✅ Estrutura que funciona:</h3>";
        echo "<pre style='background: #d4edda; padding: 10px; border: 2px solid green;'>";
        print_r($copyActivity);
        echo "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Mesmo copiando a estrutura falha</p>";
    }
}

echo "<h3>🧪 Teste 5: Verificar se precisa de BINDINGS</h3>";
$withBindings = [
    'TYPE_ID' => 1,
    'SUBJECT' => 'ZenScribe: Com BINDINGS',
    'RESPONSIBLE_ID' => 7,
    'BINDINGS' => [
        [
            'OWNER_TYPE_ID' => 1,
            'OWNER_ID' => 1
        ]
    ]
];

$result5 = CRest::call('crm.activity.add', ['fields' => $withBindings]);
echo "<p><strong>Com BINDINGS:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($result5);
echo "</pre>";

if (isset($result5['result'])) {
    echo "<p style='color: green; font-weight: bold;'>✅ BINDINGS funcionou! ID: " . $result5['result'] . "</p>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
