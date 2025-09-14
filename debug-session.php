<?php
/**
 * Debug - Verificar estado da sessão
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/settings.php');

echo "<h2>🔍 Debug - Estado da Sessão ZenScribe</h2>";

echo "<h3>📊 Session Status:</h3>";
echo "<pre>";
echo "Session Status: " . session_status() . " (1=disabled, 2=active, 3=none)\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "</pre>";

echo "<h3>🗂️ Dados da Sessão Completos:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>⚙️ Configuração ZenScribe:</h3>";
$config = getZenScribeConfig();
echo "<pre>";
print_r($config);
echo "</pre>";

echo "<h3>🔑 Verificação de Credenciais:</h3>";
echo "<ul>";
echo "<li>Google Client ID: " . (!empty($config['google']['client_id']) ? '✅ Configurado (' . substr($config['google']['client_id'], 0, 20) . '...)' : '❌ Vazio') . "</li>";
echo "<li>Google Client Secret: " . (!empty($config['google']['client_secret']) ? '✅ Configurado (' . substr($config['google']['client_secret'], 0, 10) . '...)' : '❌ Vazio') . "</li>";
echo "<li>OpenAI API Key: " . (!empty($config['openai']['api_key']) ? '✅ Configurado (' . substr($config['openai']['api_key'], 0, 10) . '...)' : '❌ Vazio') . "</li>";
echo "<li>OpenAI Habilitado: " . ($config['openai']['enabled'] ? '✅ Sim' : '❌ Não') . "</li>";
echo "</ul>";

echo "<h3>📁 Verificação de Arquivos:</h3>";
$userConfigFile = __DIR__ . '/user_config.json';
echo "<ul>";
echo "<li>user_config.json existe: " . (file_exists($userConfigFile) ? '✅ Sim' : '❌ Não') . "</li>";
if (file_exists($userConfigFile)) {
    echo "<li>Conteúdo do arquivo:</li>";
    echo "<pre>" . file_get_contents($userConfigFile) . "</pre>";
}
echo "</ul>";

echo "<h3>🕐 Timestamp:</h3>";
echo "<p>" . date('Y-m-d H:i:s') . "</p>";

echo "<hr>";
echo "<p><a href='config.php'>⚙️ Ir para Configurações</a> | <a href='test.php'>🧪 Ir para Testes</a></p>";
?>
