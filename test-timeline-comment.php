<?php
/**
 * Teste usando crm.timeline.comment.add ao invés de atividade
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>💬 Teste com Timeline Comment</h2>";

echo "<h3>🧪 Teste 1: Criar Lead para teste</h3>";
$testLead = [
    'TITLE' => 'Lead para Timeline - ' . date('H:i:s'),
    'COMMENTS' => 'Lead criado para testar timeline comments'
];

$leadResult = CRest::call('crm.lead.add', ['fields' => $testLead]);
echo "<p><strong>Criando Lead:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($leadResult);
echo "</pre>";

if (isset($leadResult['result'])) {
    $leadId = $leadResult['result'];
    echo "<p style='color: green;'>✅ Lead criado: ID $leadId</p>";
    
    echo "<h3>🧪 Teste 2: Adicionar comentário no timeline</h3>";
    $comment = [
        'ENTITY_ID' => $leadId,
        'ENTITY_TYPE' => 'lead',
        'COMMENT' => '🎯 ZenScribe: Reunião processada automaticamente\n\nResumo: Discussão sobre proposta para solução de e-commerce e gestão de estoque.\n\nLink para transcrição completa: [Google Drive]'
    ];
    
    echo "<p><strong>Comentário no timeline:</strong></p>";
    echo "<pre style='background: #e7f3ff; padding: 10px;'>";
    print_r($comment);
    echo "</pre>";
    
    $commentResult = CRest::call('crm.timeline.comment.add', $comment);
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>";
    print_r($commentResult);
    echo "</pre>";
    
    if (isset($commentResult['result'])) {
        echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO! Comentário adicionado: " . $commentResult['result'] . "</p>";
        echo "<p>✅ Lead: $leadId</p>";
        echo "<p>✅ Comentário: " . $commentResult['result'] . "</p>";
        
        echo "<h3>✅ Esta é a solução para ZenScribe!</h3>";
        echo "<p>Ao invés de criar atividades, vamos usar <strong>timeline comments</strong></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Timeline comment também falha</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Erro ao criar Lead</p>";
}

echo "<h3>🧪 Teste 3: Verificar métodos de timeline disponíveis</h3>";
$timelineMethods = [
    'crm.timeline.comment.add',
    'crm.timeline.comment.list',
    'crm.timeline.comment.fields'
];

foreach ($timelineMethods as $method) {
    if ($method === 'crm.timeline.comment.fields') {
        $result = CRest::call($method);
        echo "<p><strong>$method:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 150px; overflow: auto;'>";
        if (isset($result['result'])) {
            echo "Funcionou! " . count($result['result']) . " campos\n";
        } else {
            print_r($result);
        }
        echo "</pre>";
    }
}

echo "<h3>🧪 Teste 4: Listar comentários existentes</h3>";
$existingComments = CRest::call('crm.timeline.comment.list', [
    'filter' => ['ENTITY_TYPE' => 'lead'],
    'select' => ['ID', 'COMMENT', 'ENTITY_ID'],
    'order' => ['ID' => 'DESC']
]);

echo "<p><strong>Comentários existentes:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 200px; overflow: auto;'>";
if (isset($existingComments['result'])) {
    foreach (array_slice($existingComments['result'], 0, 5) as $comment) {
        echo "ID: {$comment['ID']} - Entity: {$comment['ENTITY_ID']} - " . substr($comment['COMMENT'], 0, 50) . "...\n";
    }
} else {
    print_r($existingComments);
}
echo "</pre>";

echo "<h3>📝 Conclusão</h3>";
echo "<p><strong>Timeline Comments podem ser a solução perfeita para ZenScribe:</strong></p>";
echo "<ul>";
echo "<li>✅ Mais simples que atividades</li>";
echo "<li>✅ Aparece no feed da entidade</li>";
echo "<li>✅ Não precisa de COMMUNICATIONS, BINDINGS, etc.</li>";
echo "<li>✅ Funciona com Lead, Deal, Contact, Company</li>";
echo "</ul>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
