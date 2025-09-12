<?php
/**
 * ZenScribe App - Installation Handler
 * Instala o app local no Bitrix24
 */

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

// Verificar se é uma instalação via Bitrix24 ou acesso direto
$isBitrix24Install = isset($_REQUEST['code']) || isset($_REQUEST['domain']);

if ($isBitrix24Install) {
    $result = CRest::installApp();
} else {
    // Modo standalone - mostrar instruções
    $result = [
        'rest_only' => false,
        'install' => false,
        'standalone' => true
    ];
}

if ($result['rest_only'] === false): ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - Instalação</title>
    <script src="//api.bitrix24.com/api/v1/"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            font-size: 3em;
            margin-bottom: 20px;
        }
        .title {
            color: #333;
            margin-bottom: 30px;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .next-steps {
            margin-top: 30px;
            text-align: left;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .next-steps h3 {
            color: #495057;
            margin-top: 0;
        }
        .next-steps ol {
            color: #6c757d;
        }
        .next-steps li {
            margin: 8px 0;
        }
    </style>
    
    <?php if ($result['install'] == true): ?>
    <script>
        BX24.init(function(){
            console.log('🎯 ZenScribe: App instalado com sucesso!');
            BX24.installFinish();
        });
    </script>
    <?endif; ?>
</head>
<body>
    <div class="container">
        <div class="logo">🎯</div>
        <h1 class="title">ZenScribe - AI Meeting Processor</h1>
        
        <?php if (isset($result['standalone']) && $result['standalone']): ?>
            <div class="status info">
                📋 Instruções de Instalação no Bitrix24
            </div>
            
            <p>Para instalar o ZenScribe no seu Bitrix24, siga os passos abaixo:</p>
            
            <div class="next-steps">
                <h3>🔧 Passo a passo:</h3>
                <ol>
                    <li><strong>Acesse seu Bitrix24:</strong> Portal → Aplicações → Desenvolvedor</li>
                    <li><strong>Crie App Local:</strong> Clique em "Outros" → "Aplicação Local"</li>
                    <li><strong>Preencha os dados:</strong>
                        <ul>
                            <li><strong>Nome:</strong> ZenScribe</li>
                            <li><strong>Código:</strong> zenscribe</li>
                            <li><strong>Caminho handler:</strong> <code>http://localhost:8000/index.php</code></li>
                            <li><strong>Caminho instalação:</strong> <code>http://localhost:8000/install.php</code></li>
                        </ul>
                    </li>
                    <li><strong>Permissões necessárias:</strong>
                        <ul>
                            <li>✅ <strong>CRM</strong> - Para acessar leads, deals, contatos</li>
                            <li>✅ <strong>user</strong> - Para identificar usuário atual</li>
                            <li>✅ <strong>calendar</strong> - Para acessar eventos do calendário (se disponível)</li>
                        </ul>
                        <em>Nota: "profile" foi removido - use apenas CRM, user e calendar</em>
                    </li>
                    <li><strong>Instale o app</strong> no seu portal</li>
                    <li><strong>Copie</strong> o Client ID e Client Secret gerados</li>
                    <li><strong>Configure</strong> as credenciais no ZenScribe</li>
                </ol>
            </div>
            
            <a href="test.php" class="btn">🧪 Página de Teste</a>
            
        <?php elseif ($result['install'] == true): ?>
            <div class="status success">
                ✅ Instalação concluída com sucesso!
            </div>
            
            <p>O ZenScribe foi instalado no seu Bitrix24 e está pronto para processar reuniões com inteligência artificial.</p>
            
            <div class="next-steps">
                <h3>🚀 Próximos passos:</h3>
                <ol>
                    <li><strong>Configure suas credenciais:</strong> Acesse a interface do ZenScribe</li>
                    <li><strong>Google APIs:</strong> Configure Client ID e Secret para acessar Calendar/Drive</li>
                    <li><strong>OpenAI (opcional):</strong> Configure API Key para processamento inteligente</li>
                    <li><strong>Teste:</strong> Processe sua primeira reunião</li>
                </ol>
            </div>
            
            <a href="index.php" class="btn">🎯 Abrir ZenScribe</a>
            
        <?php elseif (isset($result['error'])): ?>
            <div class="status error">
                ❌ Erro na instalação: <?= htmlspecialchars($result['error_description'] ?? $result['error']) ?>
            </div>
            
            <div class="next-steps">
                <h3>🔧 Como resolver:</h3>
                <ol>
                    <li>Verifique se você tem permissões de administrador</li>
                    <li>Confirme que o App ID e Secret estão corretos</li>
                    <li>Tente reinstalar o app</li>
                    <li>Contate o suporte se o problema persistir</li>
                </ol>
            </div>
            
        <?php else: ?>
            <div class="status info">
                ⏳ Instalando ZenScribe...
            </div>
            <p>Aguarde enquanto configuramos o app no seu Bitrix24.</p>
        <?php endif; ?>
        
        <div style="margin-top: 40px; font-size: 12px; color: #999;">
            ZenScribe v<?= ZENSCRIBE_VERSION ?> - AI-Powered Meeting Processing
        </div>
    </div>
</body>
</html>
<?php endif; ?>

<?php
// Log da instalação
if (isset($result['install']) && $result['install'] == true) {
    zenLog('ZenScribe app instalado com sucesso', 'info', [
        'domain' => $result['domain'] ?? 'unknown',
        'user_id' => $result['user_id'] ?? 'unknown',
        'timestamp' => date('c')
    ]);
} elseif (isset($result['error'])) {
    zenLog('Erro na instalação do ZenScribe', 'error', [
        'error' => $result['error'],
        'description' => $result['error_description'] ?? '',
        'timestamp' => date('c')
    ]);
}
?>

    <!-- Rest only mode -->
    <?php 
    if (isset($result['install']) && $result['install'] == true) {
        echo json_encode(['status' => 'success', 'message' => 'App instalado']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result['error'] ?? 'Installation failed']);
    }
    ?>
<?php endif; ?>
