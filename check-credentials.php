<?php
/**
 * Verificar se credenciais Google e OpenAI foram mantidas
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/settings.php');

echo "<h2>🔍 Verificação de Credenciais</h2>";

// Verificar configuração ZenScribe
$config = getZenScribeConfig();

echo "<h3>📊 Estado Atual das Credenciais:</h3>";
echo "<ul>";
echo "<li><strong>Google Client ID:</strong> " . (!empty($config['google']['client_id']) ? '✅ Presente (' . substr($config['google']['client_id'], 0, 20) . '...)' : '❌ Vazio') . "</li>";
echo "<li><strong>Google Client Secret:</strong> " . (!empty($config['google']['client_secret']) ? '✅ Presente (' . substr($config['google']['client_secret'], 0, 10) . '...)' : '❌ Vazio') . "</li>";
echo "<li><strong>OpenAI API Key:</strong> " . (!empty($config['openai']['api_key']) ? '✅ Presente (' . substr($config['openai']['api_key'], 0, 10) . '...)' : '❌ Vazio') . "</li>";
echo "<li><strong>OpenAI Habilitado:</strong> " . ($config['openai']['enabled'] ? '✅ Sim' : '❌ Não') . "</li>";
echo "</ul>";

echo "<h3>📁 Arquivos de Configuração:</h3>";
echo "<ul>";

// Verificar zenscribe_config.json
$zenscribeConfigFile = __DIR__ . '/zenscribe_config.json';
echo "<li><strong>zenscribe_config.json:</strong> " . (file_exists($zenscribeConfigFile) ? '✅ Existe' : '❌ Não existe') . "</li>";
if (file_exists($zenscribeConfigFile)) {
    $content = file_get_contents($zenscribeConfigFile);
    echo "<pre style='background: #f8f9fa; padding: 10px; font-size: 12px;'>" . htmlspecialchars($content) . "</pre>";
}

// Verificar settings.json (Bitrix24)
$bitrixSettingsFile = __DIR__ . '/settings.json';
echo "<li><strong>settings.json (Bitrix24):</strong> " . (file_exists($bitrixSettingsFile) ? '✅ Existe' : '❌ Não existe') . "</li>";
if (file_exists($bitrixSettingsFile)) {
    $content = file_get_contents($bitrixSettingsFile);
    echo "<pre style='background: #f8f9fa; padding: 10px; font-size: 12px;'>" . htmlspecialchars($content) . "</pre>";
}

echo "</ul>";

echo "<h3>🗂️ Dados da Sessão:</h3>";
if (isset($_SESSION['zenscribe_config'])) {
    echo "<pre style='background: #e7f3ff; padding: 10px; font-size: 12px;'>" . print_r($_SESSION['zenscribe_config'], true) . "</pre>";
} else {
    echo "<p>❌ Nenhuma configuração na sessão</p>";
}

echo "<h3>📝 Diagnóstico:</h3>";
if (empty($config['google']['client_id']) && empty($config['openai']['api_key'])) {
    echo "<p style='color: red;'>❌ <strong>PROBLEMA:</strong> As credenciais Google e OpenAI foram perdidas!</p>";
    echo "<p><strong>Solução:</strong> Você precisa reconfigurá-las em <a href='config.php'>config.php</a></p>";
} else if (empty($config['google']['client_id'])) {
    echo "<p style='color: orange;'>⚠️ <strong>PARCIAL:</strong> OpenAI OK, mas Google perdido</p>";
} else if (empty($config['openai']['api_key'])) {
    echo "<p style='color: orange;'>⚠️ <strong>PARCIAL:</strong> Google OK, mas OpenAI perdido</p>";
} else {
    echo "<p style='color: green;'>✅ <strong>TUDO OK:</strong> Todas as credenciais estão presentes!</p>";
}

echo "<hr>";
echo "<p><a href='config.php'>⚙️ Reconfigurar Credenciais</a> | <a href='test.php'>🧪 Testar Sistema</a></p>";
?>
