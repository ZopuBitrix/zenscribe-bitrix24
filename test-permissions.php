<?php
/**
 * Teste de permissões do webhook
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔐 Teste de Permissões do Webhook</h2>";

$leadId = 68119;

echo "<h3>🧪 Teste 1: Tentar atualizar TITLE (geralmente permitido)</h3>";
$titleUpdate = CRest::call('crm.lead.update', [
    'id' => $leadId,
    'fields' => ['TITLE' => 'Lead Atualizado - ' . date('H:i:s')]
]);

echo "<p><strong>Update TITLE:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($titleUpdate);
echo "</pre>";

// Verificar se funcionou
$checkTitle = CRest::call('crm.lead.get', ['id' => $leadId, 'select' => ['ID', 'TITLE']]);
echo "<p><strong>Verificação TITLE:</strong> " . htmlspecialchars($checkTitle['result']['TITLE']) . "</p>";

echo "<h3>🧪 Teste 2: Tentar atualizar OPPORTUNITY (valor)</h3>";
$oppUpdate = CRest::call('crm.lead.update', [
    'id' => $leadId,
    'fields' => ['OPPORTUNITY' => 85000]
]);

echo "<p><strong>Update OPPORTUNITY:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($oppUpdate);
echo "</pre>";

// Verificar se funcionou
$checkOpp = CRest::call('crm.lead.get', ['id' => $leadId, 'select' => ['ID', 'OPPORTUNITY']]);
echo "<p><strong>Verificação OPPORTUNITY:</strong> R$ " . number_format($checkOpp['result']['OPPORTUNITY'], 2, ',', '.') . "</p>";

echo "<h3>🧪 Teste 3: Tentar atualizar COMMENTS (nosso problema)</h3>";
$commentUpdate = CRest::call('crm.lead.update', [
    'id' => $leadId,
    'fields' => ['COMMENTS' => 'Teste simples sem formatação - ' . date('H:i:s')]
]);

echo "<p><strong>Update COMMENTS simples:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($commentUpdate);
echo "</pre>";

// Verificar se funcionou
$checkComments = CRest::call('crm.lead.get', ['id' => $leadId, 'select' => ['ID', 'COMMENTS']]);
echo "<p><strong>Verificação COMMENTS:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
echo htmlspecialchars($checkComments['result']['COMMENTS']);
echo "</pre>";

echo "<h3>🧪 Teste 4: Verificar permissões do webhook</h3>";
$permissions = CRest::call('scope');
echo "<p><strong>Permissões do webhook:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($permissions);
echo "</pre>";

echo "<h3>🧪 Teste 5: Verificar informações do usuário atual</h3>";
$currentUser = CRest::call('user.current');
echo "<p><strong>Usuário do webhook:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
if (isset($currentUser['result'])) {
    echo "ID: " . $currentUser['result']['ID'] . "\n";
    echo "Nome: " . $currentUser['result']['NAME'] . " " . $currentUser['result']['LAST_NAME'] . "\n";
    echo "Email: " . $currentUser['result']['EMAIL'] . "\n";
    echo "Admin: " . ($currentUser['result']['IS_ADMIN'] ?? 'N/A') . "\n";
} else {
    print_r($currentUser);
}
echo "</pre>";

echo "<h3>🧪 Teste 6: Tentar criar lead com COMMENTS</h3>";
$createWithComments = CRest::call('crm.lead.add', [
    'fields' => [
        'TITLE' => 'Lead com Comments - ' . date('H:i:s'),
        'COMMENTS' => 'Comentário criado junto com o lead via ZenScribe teste'
    ]
]);

echo "<p><strong>Criar lead com COMMENTS:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($createWithComments);
echo "</pre>";

if (isset($createWithComments['result'])) {
    $newLeadId = $createWithComments['result'];
    $verifyNew = CRest::call('crm.lead.get', ['id' => $newLeadId, 'select' => ['ID', 'COMMENTS']]);
    echo "<p><strong>Verificar lead criado (ID: $newLeadId):</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    echo htmlspecialchars($verifyNew['result']['COMMENTS']);
    echo "</pre>";
}

echo "<h3>📝 Diagnóstico</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<h4>🔍 Resultados esperados:</h4>";
echo "<ul>";
echo "<li>✅ <strong>TITLE update:</strong> Deve funcionar (campo comum)</li>";
echo "<li>✅ <strong>OPPORTUNITY update:</strong> Deve funcionar (campo numérico)</li>";
echo "<li>❌ <strong>COMMENTS update:</strong> Pode falhar (permissões especiais)</li>";
echo "<li>✅ <strong>COMMENTS create:</strong> Pode funcionar (no momento da criação)</li>";
echo "</ul>";

echo "<h4>💡 Soluções possíveis:</h4>";
echo "<ul>";
echo "<li>🔧 <strong>Usar campos customizados</strong> ao invés de COMMENTS</li>";
echo "<li>🔧 <strong>Criar atividades</strong> ao invés de atualizar COMMENTS</li>";
echo "<li>🔧 <strong>Configurar permissões</strong> do webhook no Bitrix24</li>";
echo "<li>🔧 <strong>Usar App Local OAuth</strong> ao invés de webhook direto</li>";
echo "</ul>";
echo "</div>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
