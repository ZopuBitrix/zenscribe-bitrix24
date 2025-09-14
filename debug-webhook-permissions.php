<?php
/**
 * Debug das permissões do webhook
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug das permissões do webhook</h2>";

echo "<h3>🧪 Teste 1: Verificar escopo/permissões do webhook</h3>";
$scope = CRest::call('scope');
echo "<p><strong>Permissões do webhook:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($scope);
echo "</pre>";

echo "<h3>🧪 Teste 2: Testar métodos que funcionam</h3>";

// Lead (sabemos que funciona)
echo "<p><strong>✅ crm.lead.list (funciona):</strong></p>";
$leadTest = CRest::call('crm.lead.list', ['select' => ['ID', 'TITLE']]);
echo "<pre style='background: #e7f3ff; padding: 10px; max-height: 150px; overflow: auto;'>";
if (isset($leadTest['result'])) {
    echo "Funcionou! " . count($leadTest['result']) . " leads encontrados\n";
} else {
    print_r($leadTest);
}
echo "</pre>";

// User (sabemos que funciona)
echo "<p><strong>✅ user.current (funciona):</strong></p>";
$userTest = CRest::call('user.current');
echo "<pre style='background: #e7f3ff; padding: 10px; max-height: 100px; overflow: auto;'>";
if (isset($userTest['result']['ID'])) {
    echo "Funcionou! ID: " . $userTest['result']['ID'] . "\n";
} else {
    print_r($userTest);
}
echo "</pre>";

echo "<h3>🧪 Teste 3: Métodos de atividade específicos</h3>";

// Listar atividades
echo "<p><strong>❓ crm.activity.list:</strong></p>";
$activityList = CRest::call('crm.activity.list', ['select' => ['ID', 'SUBJECT']]);
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 150px; overflow: auto;'>";
print_r($activityList);
echo "</pre>";

// Campos de atividade
echo "<p><strong>❓ crm.activity.fields:</strong></p>";
$activityFields = CRest::call('crm.activity.fields');
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 150px; overflow: auto;'>";
if (isset($activityFields['result'])) {
    echo "Funcionou! " . count($activityFields['result']) . " campos encontrados\n";
} else {
    print_r($activityFields);
}
echo "</pre>";

// Tipos de atividade
echo "<p><strong>❓ crm.enum.activitytype:</strong></p>";
$activityTypes = CRest::call('crm.enum.activitytype');
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 150px; overflow: auto;'>";
print_r($activityTypes);
echo "</pre>";

echo "<h3>🧪 Teste 4: Webhook URL e estrutura</h3>";
echo "<p><strong>Webhook URL configurada:</strong></p>";
echo "<pre style='background: #ffe6e6; padding: 10px;'>";
if (defined('C_REST_WEB_HOOK_URL')) {
    echo C_REST_WEB_HOOK_URL;
} else {
    echo "Webhook URL não definida!";
}
echo "</pre>";

echo "<h3>📝 Diagnóstico</h3>";
echo "<p><strong>Baseado nos resultados:</strong></p>";
echo "<ul>";
echo "<li>Se SCOPE não mostra 'crm' → Webhook sem permissão CRM</li>";
echo "<li>Se crm.activity.list falha → Sem permissão para atividades</li>";
echo "<li>Se crm.activity.fields funciona mas add falha → Permissão apenas de leitura</li>";
echo "</ul>";

echo "<p><strong>Solução provável:</strong></p>";
echo "<p>Você precisa criar um novo webhook no Bitrix24 com permissões de <strong>CRM (escrita)</strong></p>";
echo "<p>Ou usar um App Local ao invés de webhook direto.</p>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
