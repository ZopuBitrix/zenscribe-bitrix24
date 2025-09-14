<?php
/**
 * Debug da discrepância entre logs e realidade
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Debug: Discrepância Logs vs Realidade</h2>";

echo "<h3>❗ Problema identificado:</h3>";
echo "<p>Os logs dizem que o Lead 68119 foi atualizado com comentários ZenScribe, mas na verificação real não há ZenScribe no Lead.</p>";

echo "<h3>🧪 Teste 1: Verificar Lead 68119 novamente</h3>";
$lead68119 = CRest::call('crm.lead.get', ['id' => 68119]);

if (isset($lead68119['result'])) {
    $comments = $lead68119['result']['COMMENTS'] ?? '';
    echo "<p><strong>Comentários atuais (tamanho: " . strlen($comments) . "):</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; max-height: 300px; overflow: auto;'>";
    echo htmlspecialchars($comments);
    echo "</pre>";
    
    echo "<p><strong>Data de modificação:</strong> " . $lead68119['result']['DATE_MODIFY'] . "</p>";
    echo "<p><strong>Modificado por:</strong> ID " . $lead68119['result']['MODIFY_BY_ID'] . "</p>";
    
} else {
    echo "<p style='color: red;'>❌ Lead 68119 não encontrado</p>";
}

echo "<h3>🧪 Teste 2: Buscar Leads mais recentes</h3>";
$recentLeads = CRest::call('crm.lead.list', [
    'order' => ['DATE_CREATE' => 'DESC'],
    'select' => ['ID', 'TITLE', 'DATE_CREATE', 'COMMENTS'],
    'filter' => ['>=DATE_CREATE' => '2025-09-14 18:50:00'],
    'start' => 0
]);

echo "<p><strong>Leads criados após 18:50 (hora da nossa última simulação):</strong></p>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>ID</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Título</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Data</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>ZenScribe?</th>";
echo "</tr>";

if (isset($recentLeads['result'])) {
    foreach ($recentLeads['result'] as $lead) {
        $hasZenScribe = stripos($lead['COMMENTS'], 'zenscribe') !== false;
        $rowStyle = $hasZenScribe ? 'background: #d4edda;' : '';
        
        echo "<tr style='$rowStyle'>";
        echo "<td style='border: 1px solid #ddd; padding: 8px; font-weight: bold;'>" . $lead['ID'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($lead['TITLE']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $lead['DATE_CREATE'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($hasZenScribe ? "✅ SIM" : "❌ NÃO") . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4' style='border: 1px solid #ddd; padding: 8px;'>Nenhum Lead encontrado</td></tr>";
}
echo "</table>";

echo "<h3>🧪 Teste 3: Reproduzir exatamente o que os logs dizem</h3>";
echo "<p>Vamos tentar fazer exatamente o que os logs mostram que foi feito:</p>";

// Simular exatamente o que foi logado
$leadId = 68119;
$existingComments = "Discussão sobre proposta para solução de e-commerce e gestão de estoque.";

$newComment = "\n\n==================================================\n";
$newComment .= "🎯 ZenScribe: Cliente\n";
$newComment .= "📅 14/09/2025 18:55:11\n";
$newComment .= "--------------------------------------------------\n\n";
$newComment .= "Discussão sobre proposta para solução de e-commerce e gestão de estoque.\n\n";
$newComment .= "📊 DADOS EXTRAÍDOS:\n";
$newComment .= "• COMPANY: Cliente\n";
$newComment .= "• CNPJ: 12.345.678/0001-90\n";
$newComment .= "• PHONE: (11) 99999-8888\n\n";
$newComment .= "🔗 Processado automaticamente pelo ZenScribe\n";
$newComment .= "==================================================";

$finalComments = $existingComments . $newComment;

echo "<p><strong>Comentários que DEVERIAM estar no Lead:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px; border: 1px solid #007bff;'>";
echo htmlspecialchars($finalComments);
echo "</pre>";

echo "<p><strong>Tamanho esperado:</strong> " . strlen($finalComments) . " caracteres</p>";

echo "<h3>🧪 Teste 4: Tentar update manual para confirmar</h3>";
$manualUpdate = CRest::call('crm.lead.update', [
    'id' => $leadId,
    'fields' => ['COMMENTS' => $finalComments]
]);

echo "<p><strong>Resultado do update manual:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($manualUpdate);
echo "</pre>";

if (isset($manualUpdate['result'])) {
    echo "<p style='color: green;'>✅ Update manual funcionou!</p>";
    
    // Verificar imediatamente
    $verifyLead = CRest::call('crm.lead.get', ['id' => $leadId]);
    echo "<p><strong>Verificação imediata:</strong></p>";
    $newComments = $verifyLead['result']['COMMENTS'] ?? '';
    echo "<p>Tamanho agora: " . strlen($newComments) . " caracteres</p>";
    echo "<p>Contém ZenScribe? " . (stripos($newComments, 'zenscribe') !== false ? "✅ SIM" : "❌ NÃO") . "</p>";
    
} else {
    echo "<p style='color: red;'>❌ Update manual falhou</p>";
}

echo "<h3>📝 Diagnóstico</h3>";
echo "<ul>";
echo "<li>🔍 <strong>Hipótese 1:</strong> O update foi bem-sucedido mas algum processo do Bitrix24 desfez as alterações</li>";
echo "<li>🔍 <strong>Hipótese 2:</strong> Há um cache ou delay entre o update e a visualização</li>";
echo "<li>🔍 <strong>Hipótese 3:</strong> O ID do Lead nos logs não corresponde ao Lead real</li>";
echo "<li>🔍 <strong>Hipótese 4:</strong> Existe outro Lead (68120, 68121...) que realmente tem os comentários</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
