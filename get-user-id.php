<?php
/**
 * Descobrir ID do usuário atual
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>👤 Descobrir seu ID de usuário</h2>";

echo "<h3>🔍 Usuário atual (user.current)</h3>";
$currentUser = CRest::call('user.current');
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($currentUser);
echo "</pre>";

if (isset($currentUser['result']['ID'])) {
    $userId = $currentUser['result']['ID'];
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>✅ SEU ID: $userId</p>";
    echo "<p>Nome: " . ($currentUser['result']['NAME'] ?? 'N/A') . " " . ($currentUser['result']['LAST_NAME'] ?? '') . "</p>";
    echo "<p>Email: " . ($currentUser['result']['EMAIL'] ?? 'N/A') . "</p>";
} else {
    echo "<p style='color: red;'>❌ Erro ao obter usuário atual</p>";
    
    // Tentar listar usuários
    echo "<h3>👥 Lista de usuários</h3>";
    $users = CRest::call('user.get', ['ACTIVE' => true]);
    echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow: auto;'>";
    if (isset($users['result'])) {
        foreach ($users['result'] as $user) {
            echo "ID: {$user['ID']} - {$user['NAME']} {$user['LAST_NAME']} ({$user['EMAIL']})\n";
        }
    } else {
        print_r($users);
    }
    echo "</pre>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
