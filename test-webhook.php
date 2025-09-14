<?php
/**
 * Teste direto do webhook Bitrix24
 */

// Headers para debug
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Teste do Webhook Bitrix24</h2>";

// Testar webhook diretamente
$webhookUrl = 'https://zopu.bitrix24.com.br/rest/7/ch1ivgu881vzusdq/';

echo "<h3>📡 Testando conexão direta com webhook...</h3>";
echo "<p><strong>URL:</strong> $webhookUrl</p>";

// Teste 1: user.current (mais simples)
echo "<h4>Teste 1: user.current</h4>";
$testUrl = $webhookUrl . 'user.current.json';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
if ($error) {
    echo "<p style='color: red;'><strong>Erro cURL:</strong> $error</p>";
}

echo "<p><strong>Resposta:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow: auto;'>";
echo htmlspecialchars($response);
echo "</pre>";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['result'])) {
        echo "<p style='color: green;'>✅ <strong>WEBHOOK FUNCIONANDO!</strong></p>";
        echo "<p>Usuário: " . ($data['result']['NAME'] ?? 'N/A') . " (" . ($data['result']['ID'] ?? 'N/A') . ")</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Resposta válida mas sem 'result'</p>";
    }
} else {
    echo "<p style='color: red;'>❌ <strong>WEBHOOK COM PROBLEMA!</strong></p>";
    
    if ($httpCode === 401) {
        echo "<p>🚨 <strong>Diagnóstico HTTP 401:</strong></p>";
        echo "<ul>";
        echo "<li>Token do webhook pode ter expirado</li>";
        echo "<li>Permissões insuficientes</li>";
        echo "<li>URL do webhook incorreta</li>";
        echo "<li>Webhook pode ter sido deletado/revogado</li>";
        echo "</ul>";
    }
}

// Teste 2: Verificar se é problema de método
echo "<hr><h4>Teste 2: profile (método alternativo)</h4>";
$testUrl2 = $webhookUrl . 'profile.json';

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $testUrl2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, '');
curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "<p><strong>HTTP Code:</strong> $httpCode2</p>";
echo "<p><strong>Resposta:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
echo htmlspecialchars($response2);
echo "</pre>";

// Teste 3: Verificar formato do webhook
echo "<hr><h4>Teste 3: Análise da URL</h4>";
$urlParts = parse_url($webhookUrl);
echo "<p><strong>Host:</strong> " . ($urlParts['host'] ?? 'N/A') . "</p>";
echo "<p><strong>Path:</strong> " . ($urlParts['path'] ?? 'N/A') . "</p>";

if (preg_match('/\/rest\/(\d+)\/([a-zA-Z0-9]+)\/$/', $urlParts['path'], $matches)) {
    echo "<p style='color: green;'>✅ Formato de URL válido</p>";
    echo "<p>User ID: {$matches[1]}</p>";
    echo "<p>Token: " . substr($matches[2], 0, 10) . "...</p>";
} else {
    echo "<p style='color: red;'>❌ Formato de URL inválido</p>";
}

echo "<hr>";
echo "<h3>🔧 Possíveis soluções:</h3>";
echo "<ol>";
echo "<li><strong>Recriar webhook:</strong> Bitrix24 → Aplicações → Webhooks → Criar novo</li>";
echo "<li><strong>Verificar permissões:</strong> Marcar todas as opções CRM + user</li>";
echo "<li><strong>Testar no navegador:</strong> <a href='$testUrl' target='_blank'>$testUrl</a></li>";
echo "<li><strong>Verificar se webhook não expirou</strong></li>";
echo "</ol>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
