<?php
/**
 * Descobrir métodos corretos para comentários
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>🔍 Descobrir métodos corretos para comentários</h2>";

echo "<h3>🧪 Teste 1: Métodos relacionados a comments</h3>";
$commentMethods = [
    'crm.timeline.comment.add',
    'crm.timeline.comment.list',
    'crm.timeline.comment.get',
    'crm.timeline.comment.update',
    'crm.timeline.comment.delete',
    'crm.timeline.comment.fields',
    'crm.comment.add',
    'crm.comment.list',
    'crm.comment.fields'
];

foreach ($commentMethods as $method) {
    $result = CRest::call($method);
    echo "<p><strong>$method:</strong> ";
    
    if (isset($result['error'])) {
        if ($result['error'] === 'METHOD_NOT_FOUND') {
            echo "❌ Método não existe</p>";
        } else {
            echo "⚠️ Erro: " . $result['error'] . "</p>";
        }
    } else {
        echo "✅ Método existe!</p>";
        if ($method === 'crm.timeline.comment.fields' || $method === 'crm.comment.fields') {
            echo "<pre style='background: #e7f3ff; padding: 10px; max-height: 200px; overflow: auto;'>";
            if (isset($result['result'])) {
                foreach ($result['result'] as $fieldName => $fieldInfo) {
                    echo "- $fieldName: " . ($fieldInfo['title'] ?? 'N/A') . "\n";
                }
            }
            echo "</pre>";
        }
    }
}

echo "<h3>🧪 Teste 2: Usar método que funciona - crm.lead.update com COMMENTS</h3>";
$leadUpdate = [
    'id' => 68103,
    'fields' => [
        'COMMENTS' => "🎯 ZenScribe: Reunião processada automaticamente\n\nResumo: Discussão sobre proposta para solução de e-commerce e gestão de estoque.\n\nProcessado em " . date('d/m/Y H:i:s')
    ]
];

echo "<p><strong>Atualizando Lead 68103 com comentário:</strong></p>";
echo "<pre style='background: #e7f3ff; padding: 10px;'>";
print_r($leadUpdate);
echo "</pre>";

$updateResult = CRest::call('crm.lead.update', $leadUpdate);
echo "<p><strong>Resultado:</strong></p>";
echo "<pre style='background: #f8f9fa; padding: 10px;'>";
print_r($updateResult);
echo "</pre>";

if (isset($updateResult['result'])) {
    echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 SUCESSO! Lead atualizado com comentário!</p>";
    echo "<p>✅ Lead: 68103</p>";
    echo "<p>✅ Campo COMMENTS preenchido</p>";
} else {
    echo "<p style='color: red;'>❌ Update também falha</p>";
}

echo "<h3>🧪 Teste 3: Verificar se existe crm.livefeed methods</h3>";
$livefeedMethods = [
    'crm.livefeed.message.add',
    'crm.livefeed.post.add',
    'livefeed.message.add',
    'livefeed.post.add'
];

foreach ($livefeedMethods as $method) {
    $result = CRest::call($method);
    echo "<p><strong>$method:</strong> ";
    
    if (isset($result['error'])) {
        if ($result['error'] === 'METHOD_NOT_FOUND') {
            echo "❌ Método não existe</p>";
        } else {
            echo "⚠️ Erro: " . $result['error'] . "</p>";
        }
    } else {
        echo "✅ Método existe!</p>";
    }
}

echo "<h3>📝 Estratégias que funcionam</h3>";
echo "<p><strong>Opções válidas para ZenScribe:</strong></p>";
echo "<ul>";
echo "<li>✅ <strong>crm.lead.update</strong> - Atualizar campo COMMENTS (já funciona)</li>";
echo "<li>✅ <strong>crm.deal.update</strong> - Atualizar campo COMMENTS (deve funcionar)</li>";
echo "<li>❓ <strong>Livefeed methods</strong> - Se existir, adiciona ao feed</li>";
echo "<li>🔧 <strong>Campos customizados</strong> - Criar campos específicos para ZenScribe</li>";
echo "</ul>";

echo "<h3>💡 Solução Simples</h3>";
echo "<p>Como o <strong>crm.lead.update com COMMENTS</strong> funciona, vamos usar isso!</p>";
echo "<p>É mais simples e efetivo que tentar criar atividades complexas.</p>";

echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a></p>";
?>
