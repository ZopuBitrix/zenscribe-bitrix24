<?php
/**
 * Teste específico do crm.lead.update que está falhando
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Teste do crm.lead.update Específico</h2>";

// Dados exatos que estão falhando
$leadId = 67560;
$fields = [
    'TITLE' => 'Cliente',
    'COMMENTS' => 'Discussão sobre proposta para solução de e-commerce e gestão de estoque.',
    'OPPORTUNITY' => 75000
];

echo "<h3>📊 Dados que estão causando erro:</h3>";
echo "<ul>";
echo "<li><strong>Lead ID:</strong> $leadId</li>";
echo "<li><strong>TITLE:</strong> " . htmlspecialchars($fields['TITLE']) . "</li>";
echo "<li><strong>COMMENTS:</strong> " . htmlspecialchars($fields['COMMENTS']) . "</li>";
echo "<li><strong>OPPORTUNITY:</strong> " . $fields['OPPORTUNITY'] . "</li>";
echo "</ul>";

echo "<h3>🧪 Teste 1: Verificar se lead existe</h3>";
$leadCheck = CRest::call('crm.lead.get', ['id' => $leadId]);
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
print_r($leadCheck);
echo "</pre>";

if (isset($leadCheck['error'])) {
    echo "<p style='color: red;'>❌ <strong>Lead não existe!</strong> Isso explica o erro 400.</p>";
    echo "<p>Vamos testar criação de novo lead...</p>";
    
    echo "<h3>🧪 Teste 2: Criar novo lead</h3>";
    $createResult = CRest::call('crm.lead.add', ['fields' => $fields]);
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    print_r($createResult);
    echo "</pre>";
    
    if (isset($createResult['result'])) {
        echo "<p style='color: green;'>✅ Lead criado com sucesso! ID: " . $createResult['result'] . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Lead existe! Vamos testar os campos individualmente...</p>";
    
    echo "<h3>🧪 Teste 2: Campos obrigatórios</h3>";
    $leadFields = CRest::call('crm.lead.fields');
    if (isset($leadFields['result'])) {
        echo "<p><strong>Campos obrigatórios:</strong></p>";
        echo "<ul>";
        foreach ($leadFields['result'] as $fieldName => $fieldInfo) {
            if (isset($fieldInfo['isRequired']) && $fieldInfo['isRequired']) {
                echo "<li><strong>$fieldName</strong>: " . ($fieldInfo['title'] ?? 'N/A') . "</li>";
            }
        }
        echo "</ul>";
        
        // Verificar campos específicos
        echo "<p><strong>Verificação dos nossos campos:</strong></p>";
        echo "<ul>";
        $ourFields = ['TITLE', 'COMMENTS', 'OPPORTUNITY'];
        foreach ($ourFields as $field) {
            if (isset($leadFields['result'][$field])) {
                $info = $leadFields['result'][$field];
                echo "<li><strong>$field</strong>: ";
                echo "Tipo=" . ($info['type'] ?? 'N/A');
                echo ", Obrigatório=" . (isset($info['isRequired']) && $info['isRequired'] ? 'Sim' : 'Não');
                echo ", Editável=" . (isset($info['isReadOnly']) && $info['isReadOnly'] ? 'Não' : 'Sim');
                echo "</li>";
            } else {
                echo "<li><strong>$field</strong>: ❌ Campo não existe!</li>";
            }
        }
        echo "</ul>";
    }
    
    echo "<h3>🧪 Teste 3: Update com um campo por vez</h3>";
    
    // Testar TITLE
    echo "<h4>3.1 Testando só TITLE:</h4>";
    $titleResult = CRest::call('crm.lead.update', [
        'id' => $leadId,
        'fields' => ['TITLE' => $fields['TITLE']]
    ]);
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($titleResult);
    echo "</pre>";
    
    // Testar COMMENTS
    echo "<h4>3.2 Testando só COMMENTS:</h4>";
    $commentsResult = CRest::call('crm.lead.update', [
        'id' => $leadId,
        'fields' => ['COMMENTS' => $fields['COMMENTS']]
    ]);
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($commentsResult);
    echo "</pre>";
    
    // Testar OPPORTUNITY
    echo "<h4>3.3 Testando só OPPORTUNITY:</h4>";
    $oppResult = CRest::call('crm.lead.update', [
        'id' => $leadId,
        'fields' => ['OPPORTUNITY' => $fields['OPPORTUNITY']]
    ]);
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($oppResult);
    echo "</pre>";
    
    // Testar OPPORTUNITY como string
    echo "<h4>3.4 Testando OPPORTUNITY como string:</h4>";
    $oppStringResult = CRest::call('crm.lead.update', [
        'id' => $leadId,
        'fields' => ['OPPORTUNITY' => '75000.00']
    ]);
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($oppStringResult);
    echo "</pre>";
}

echo "<hr>";
echo "<h3>🔧 Diagnóstico:</h3>";
echo "<p>Com base nos testes acima, podemos identificar:</p>";
echo "<ul>";
echo "<li>Se o Lead existe</li>";
echo "<li>Quais campos são obrigatórios</li>";
echo "<li>Qual campo específico está causando o erro</li>";
echo "<li>Se é um problema de tipo de dados (número vs string)</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
