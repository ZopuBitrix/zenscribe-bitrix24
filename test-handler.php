<?php
/**
 * Teste direto do handler.php
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Teste do Handler ZenScribe</h2>";

echo "<h3>🧪 Simulando chamada processLatestMeeting...</h3>";

// Capturar output
ob_start();

try {
    // Simular a chamada POST que o frontend faz
    $_POST['action'] = 'process_latest_meeting';
    
    // Capturar qualquer output do handler
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://zenscribe-bitrix24-production.up.railway.app/handler.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'process_latest_meeting']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
    
    if ($error) {
        echo "<p style='color: red;'><strong>Erro cURL:</strong> $error</p>";
    }
    
    echo "<p><strong>Resposta do Handler:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 400px; overflow: auto;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
    
    // Tentar parsear JSON
    $jsonData = json_decode($response, true);
    if ($jsonData) {
        echo "<h4>📊 Dados estruturados:</h4>";
        echo "<pre style='background: #e7f3ff; padding: 10px; max-height: 300px; overflow: auto;'>";
        print_r($jsonData);
        echo "</pre>";
        
        if (isset($jsonData['error'])) {
            echo "<p style='color: red;'>❌ <strong>Erro encontrado:</strong> " . htmlspecialchars($jsonData['message']) . "</p>";
        } else {
            echo "<p style='color: green;'>✅ <strong>Sucesso!</strong> " . htmlspecialchars($jsonData['message'] ?? 'Processamento concluído') . "</p>";
        }
    }
    
    if ($httpCode === 400) {
        echo "<hr><h3>🚨 Diagnóstico HTTP 400:</h3>";
        echo "<p>O erro 400 (Bad Request) indica que há algo errado na requisição ou no processamento.</p>";
        echo "<p><strong>Possíveis causas:</strong></p>";
        echo "<ul>";
        echo "<li>Erro na função <code>getLatestGoogleMeeting()</code></li>";
        echo "<li>Erro na função <code>findMeetingTranscript()</code></li>";
        echo "<li>Erro na função <code>processTranscript()</code></li>";
        echo "<li>Erro na função <code>updateBitrixEntity()</code></li>";
        echo "<li>Problema com as credenciais Google ou OpenAI</li>";
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Exceção capturada:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

$output = ob_get_clean();
echo $output;

echo "<hr>";
echo "<h3>🔧 Testes individuais:</h3>";

// Teste 1: Verificar credenciais
echo "<h4>1. Verificar credenciais</h4>";
require_once(__DIR__ . '/settings.php');
$config = getZenScribeConfig();

echo "<ul>";
echo "<li>Google Client ID: " . (!empty($config['google']['client_id']) ? '✅ OK' : '❌ Faltando') . "</li>";
echo "<li>OpenAI API Key: " . (!empty($config['openai']['api_key']) ? '✅ OK' : '❌ Faltando') . "</li>";
echo "<li>OpenAI Habilitado: " . ($config['openai']['enabled'] ? '✅ Sim' : '⚠️ Não') . "</li>";
echo "</ul>";

// Teste 2: Testar funções individualmente  
echo "<h4>2. Testar funções core</h4>";
try {
    require_once(__DIR__ . '/handler.php');
    
    echo "<p>🧪 <strong>getLatestGoogleMeeting():</strong> ";
    $meeting = getLatestGoogleMeeting();
    if ($meeting) {
        echo "✅ OK (" . $meeting['title'] . ")</p>";
    } else {
        echo "❌ Falhou</p>";
    }
    
    echo "<p>🧪 <strong>findMeetingTranscript():</strong> ";
    $transcript = findMeetingTranscript($meeting);
    if ($transcript) {
        echo "✅ OK (" . strlen($transcript) . " chars)</p>";
    } else {
        echo "❌ Falhou</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao testar funções: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a> | <a href='debug-bitrix-calls.php'>🔍 Debug Bitrix24</a></p>";
?>
