<?php
/**
 * Debug específico do Lead 68115
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug Lead 68115 (mais recente)</h2>";

echo "<h3>📋 Dados completos do Lead 68115</h3>";
$lead = CRest::call('crm.lead.get', ['id' => 68115]);

if (isset($lead['result'])) {
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    foreach ($lead['result'] as $field => $value) {
        if (is_array($value)) {
            $value = json_encode($value, JSON_PRETTY_PRINT);
        }
        
        $highlight = ($field === 'COMMENTS') ? 'background: #ffe6e6;' : '';
        
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 8px; font-weight: bold; $highlight'>$field</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px; $highlight'>" . htmlspecialchars($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>💬 Comentários detalhados</h3>";
    $comments = $lead['result']['COMMENTS'] ?? '';
    echo "<p><strong>Tamanho:</strong> " . strlen($comments) . " caracteres</p>";
    echo "<p><strong>Contém 'ZenScribe'?</strong> " . (stripos($comments, 'zenscribe') !== false ? "✅ SIM" : "❌ NÃO") . "</p>";
    echo "<p><strong>Contém '🎯'?</strong> " . (stripos($comments, '🎯') !== false ? "✅ SIM" : "❌ NÃO") . "</p>";
    
    echo "<h4>📝 Texto completo dos comentários:</h4>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow: auto;'>";
    echo htmlspecialchars($comments);
    echo "</pre>";
    
    echo "<h3>🧪 Teste: Adicionar comentário ZenScribe manualmente</h3>";
    
    $newComment = "\n\n" . str_repeat("=", 50) . "\n";
    $newComment .= "🎯 ZenScribe: TESTE MANUAL\n";
    $newComment .= "📅 " . date('d/m/Y H:i:s') . "\n";
    $newComment .= str_repeat("-", 50) . "\n\n";
    $newComment .= "TESTE: Adicionando comentário ZenScribe manualmente\n\n";
    $newComment .= "📊 DADOS EXTRAÍDOS:\n";
    $newComment .= "• COMPANY: ACME Corp\n";
    $newComment .= "• PHONE: (11) 99999-8888\n";
    $newComment .= "\n🔗 Processado automaticamente pelo ZenScribe\n";
    $newComment .= str_repeat("=", 50);
    
    $finalComments = $comments . $newComment;
    
    echo "<p><strong>Tentando adicionar comentário ZenScribe:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px; border: 1px solid #007bff;'>";
    echo "Comentário a adicionar:\n";
    echo htmlspecialchars($newComment);
    echo "</pre>";
    
    $updateResult = CRest::call('crm.lead.update', [
        'id' => 68115,
        'fields' => ['COMMENTS' => $finalComments]
    ]);
    
    echo "<p><strong>Resultado do update:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($updateResult);
    echo "</pre>";
    
    if (isset($updateResult['result'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ Update manual funcionou!</p>";
        echo "<p>O problema não é com o update em si, mas com o fluxo do ZenScribe.</p>";
        
        // Verificar resultado
        $updatedLead = CRest::call('crm.lead.get', ['id' => 68115]);
        echo "<h4>📋 Comentários após update manual:</h4>";
        echo "<pre style='background: #d4edda; padding: 10px; max-height: 300px; overflow: auto;'>";
        echo htmlspecialchars($updatedLead['result']['COMMENTS']);
        echo "</pre>";
        
    } else {
        echo "<p style='color: red;'>❌ Update manual também falhou</p>";
        echo "<p>Erro: " . json_encode($updateResult) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Erro ao buscar Lead 68115</p>";
    echo "<pre>";
    print_r($lead);
    echo "</pre>";
}

echo "<h3>📝 Diagnóstico</h3>";
echo "<p><strong>Problemas identificados:</strong></p>";
echo "<ul>";
echo "<li>❌ Leads são criados mas comentários ZenScribe não são adicionados</li>";
echo "<li>⚠️ Função createRichActivity() está falhando silenciosamente</li>";
echo "<li>🔧 Precisa debuggar especificamente o fluxo de comentários</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
