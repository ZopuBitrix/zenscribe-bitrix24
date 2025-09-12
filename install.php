<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - Instalação</title>
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
        code {
            background: #e9ecef;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🎯</div>
        <h1 class="title">ZenScribe - AI Meeting Processor</h1>
        
        <div class="status">
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
                        <li><strong>Caminho instalação:</strong> <code>http://localhost:8000/install-simple.php</code></li>
                    </ul>
                </li>
                <li><strong>Permissões necessárias:</strong>
                    <ul>
                        <li>✅ <strong>CRM</strong> - Para acessar leads, deals, contatos</li>
                        <li>✅ <strong>user</strong> - Para identificar usuário atual</li>
                        <li>✅ <strong>calendar</strong> - Para acessar eventos do calendário (se disponível)</li>
                    </ul>
                    <em style="color: #dc3545;">⚠️ Nota: NÃO use "profile" - essa permissão não existe mais</em>
                </li>
                <li><strong>Instale o app</strong> no seu portal</li>
                <li><strong>Copie</strong> o Client ID e Client Secret gerados</li>
                <li><strong>Configure</strong> as credenciais no ZenScribe</li>
            </ol>
        </div>
        
        <div style="margin: 30px 0;">
            <a href="test.php" class="btn">🧪 Página de Teste</a>
            <a href="index.php" class="btn">🎯 Dashboard Principal</a>
        </div>
        
        <div style="margin-top: 40px; font-size: 12px; color: #999;">
            ZenScribe v2.0.0 - AI-Powered Meeting Processing<br>
            Status do servidor: <span style="color: #28a745;">✅ PHP <?= phpversion() ?> rodando</span>
        </div>
    </div>
</body>
</html>
