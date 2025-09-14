<?php
/**
 * Verificar Leads criados recentemente
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Verificar Leads Recentes</h2>";

echo "<h3>🧪 Últimos 10 Leads criados</h3>";
$recentLeads = CRest::call('crm.lead.list', [
    'order' => ['DATE_CREATE' => 'DESC'],
    'select' => ['ID', 'TITLE', 'DATE_CREATE', 'COMMENTS'],
    'filter' => [],
    'start' => 0
]);

echo "<p><strong>Leads mais recentes:</strong></p>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>ID</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Título</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Data Criação</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Comentários (primeiros 100 chars)</th>";
echo "</tr>";

if (isset($recentLeads['result'])) {
    foreach (array_slice($recentLeads['result'], 0, 10) as $lead) {
        $hasZenScribe = stripos($lead['COMMENTS'], 'zenscribe') !== false;
        $rowStyle = $hasZenScribe ? 'background: #d4edda;' : '';
        
        echo "<tr style='$rowStyle'>";
        echo "<td style='border: 1px solid #ddd; padding: 8px; font-weight: bold;'>" . $lead['ID'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($lead['TITLE']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $lead['DATE_CREATE'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars(substr($lead['COMMENTS'], 0, 100)) . "...</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4' style='border: 1px solid #ddd; padding: 8px; text-align: center;'>Erro ao buscar leads</td></tr>";
}

echo "</table>";

echo "<h3>🎯 Leads com 'ZenScribe' nos comentários</h3>";
$zenscribeLeads = CRest::call('crm.lead.list', [
    'order' => ['DATE_CREATE' => 'DESC'],
    'select' => ['ID', 'TITLE', 'DATE_CREATE', 'COMMENTS'],
    'filter' => ['%COMMENTS' => 'ZenScribe'],
    'start' => 0
]);

echo "<p><strong>Leads processados pelo ZenScribe:</strong></p>";

if (isset($zenscribeLeads['result']) && !empty($zenscribeLeads['result'])) {
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #e7f3ff;'>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>ID</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Título</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Data</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Status ZenScribe</th>";
    echo "</tr>";
    
    foreach ($zenscribeLeads['result'] as $lead) {
        echo "<tr style='background: #d4edda;'>";
        echo "<td style='border: 1px solid #ddd; padding: 8px; font-weight: bold;'>" . $lead['ID'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($lead['TITLE']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $lead['DATE_CREATE'] . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Processado</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>📋 Detalhes do último Lead ZenScribe</h3>";
    $lastZenLead = $zenscribeLeads['result'][0];
    $fullLead = CRest::call('crm.lead.get', ['id' => $lastZenLead['ID']]);
    
    echo "<p><strong>Lead ID: " . $lastZenLead['ID'] . "</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 400px; overflow: auto;'>";
    echo "TÍTULO: " . $fullLead['result']['TITLE'] . "\n";
    echo "DATA: " . $fullLead['result']['DATE_CREATE'] . "\n";
    echo "RESPONSÁVEL: " . ($fullLead['result']['ASSIGNED_BY_ID'] ?? 'N/A') . "\n\n";
    echo "COMENTÁRIOS:\n";
    echo "----------------------------------------\n";
    echo $fullLead['result']['COMMENTS'];
    echo "</pre>";
    
} else {
    echo "<p style='color: orange;'>⚠️ Nenhum Lead com 'ZenScribe' encontrado nos comentários</p>";
    echo "<p>Isso pode significar:</p>";
    echo "<ul>";
    echo "<li>O Lead foi criado mas os comentários não foram atualizados</li>";
    echo "<li>O filtro de busca não está funcionando</li>";
    echo "<li>O processamento falhou silenciosamente</li>";
    echo "</ul>";
}

echo "<h3>🔍 Verificar Leads específicos</h3>";
$specificIds = [68113, 68114, 68115, 68116, 68117];

foreach ($specificIds as $id) {
    $checkLead = CRest::call('crm.lead.get', ['id' => $id]);
    echo "<p><strong>Lead $id:</strong> ";
    
    if (isset($checkLead['result'])) {
        echo "✅ Existe - " . htmlspecialchars($checkLead['result']['TITLE']);
        if (stripos($checkLead['result']['COMMENTS'], 'zenscribe') !== false) {
            echo " 🎯 (com ZenScribe)";
        }
    } else {
        echo "❌ Não existe";
    }
    echo "</p>";
}

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
