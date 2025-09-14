<?php
/**
 * Debug da criação de Lead
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug da criação de Lead</h2>";

echo "<h3>🧪 Teste 1: Listar últimos Leads criados</h3>";
$recentLeads = CRest::call('crm.lead.list', [
    'order' => ['DATE_CREATE' => 'DESC'],
    'filter' => ['TITLE' => 'Cliente'],
    'select' => ['ID', 'TITLE', 'DATE_CREATE', 'COMMENTS']
]);

echo "<p><strong>Últimos Leads com título 'Cliente':</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 400px; overflow: auto;'>";
if (isset($recentLeads['result'])) {
    foreach ($recentLeads['result'] as $lead) {
        echo "ID: {$lead['ID']} - {$lead['TITLE']} - Criado: {$lead['DATE_CREATE']}\n";
        if (!empty($lead['COMMENTS'])) {
            echo "  Comentário: " . substr($lead['COMMENTS'], 0, 100) . "...\n";
        }
        echo "\n";
    }
} else {
    print_r($recentLeads);
}
echo "</pre>";

echo "<h3>🧪 Teste 2: Criar Lead simples para teste</h3>";
$testLead = [
    'TITLE' => 'Lead de Teste ZenScribe - ' . date('H:i:s'),
    'COMMENTS' => 'Lead criado para testar atividades'
];

echo "<p><strong>Criando Lead:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($testLead);
echo "</pre>";

$leadResult = CRest::call('crm.lead.add', ['fields' => $testLead]);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($leadResult);
echo "</pre>";

if (isset($leadResult['result'])) {
    $newLeadId = $leadResult['result'];
    echo "<p style='color: green; font-weight: bold;'>✅ Lead criado com sucesso! ID: $newLeadId</p>";
    
    echo "<h3>🧪 Teste 3: Criar atividade para o Lead que REALMENTE existe</h3>";
    $workingActivity = [
        'OWNER_TYPE_ID' => 1,
        'OWNER_ID' => $newLeadId,
        'TYPE_ID' => 2,
        'SUBJECT' => 'ZenScribe: Teste com Lead existente',
        'DESCRIPTION' => 'Atividade para Lead que realmente existe',
        'COMPLETED' => 'Y',
        'RESPONSIBLE_ID' => 7,
        'COMMUNICATIONS' => [
            [
                'TYPE' => 'OTHER',
                'VALUE' => 'Google Meet - ZenScribe'
            ]
        ]
    ];
    
    echo "<p><strong>Atividade para Lead $newLeadId:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    print_r($workingActivity);
    echo "</pre>";
    
    $activityResult = CRest::call('crm.activity.add', ['fields' => $workingActivity]);
    echo "<p><strong>Resultado da atividade:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($activityResult);
    echo "</pre>";
    
    if (isset($activityResult['result'])) {
        echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO TOTAL!</p>";
        echo "<p>✅ Lead criado: ID $newLeadId</p>";
        echo "<p>✅ Atividade criada: ID " . $activityResult['result'] . "</p>";
        echo "<p><strong>O problema era que o Lead 67560 não existia!</strong></p>";
    } else {
        echo "<p style='color: red;'>❌ Atividade ainda falha mesmo com Lead existente</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Erro ao criar Lead de teste</p>";
}

echo "<h3>📝 Diagnóstico</h3>";
echo "<p><strong>O problema identificado:</strong></p>";
echo "<ul>";
echo "<li>❌ Lead 67560 não existe no Bitrix24</li>";
echo "<li>⚠️ O processo de criação de Lead no handler.php pode estar falhando silenciosamente</li>";
echo "<li>🔧 Precisamos verificar se o Lead foi realmente criado e usar o ID correto</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
