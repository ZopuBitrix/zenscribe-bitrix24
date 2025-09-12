<?php
/**
 * ZenScribe - Teste Básico
 * Teste rápido para verificar se o servidor PHP está funcionando
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - Teste de Servidor</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            font-size: 4em;
            margin-bottom: 20px;
        }
        .title {
            color: #333;
            margin-bottom: 30px;
        }
        .status {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
        }
        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .info h3 {
            color: #495057;
            margin-top: 0;
        }
        .info ul {
            color: #6c757d;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🎯</div>
        <h1 class="title">ZenScribe - AI Meeting Processor</h1>
        
        <div class="status">
            ✅ Servidor PHP funcionando corretamente!
        </div>
        
        <p><strong>Versão PHP:</strong> <?= phpversion() ?></p>
        <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i:s') ?></p>
        <p><strong>Status:</strong> Pronto para instalação no Bitrix24</p>
        
        <div class="info">
            <h3>🚀 Próximos passos para instalação:</h3>
            <ol>
                <li><strong>Acesse seu Bitrix24:</strong> Portal → Aplicações → Desenvolvedor</li>
                <li><strong>Crie App Local:</strong> Outros → Aplicação Local</li>
                <li><strong>Configure as URLs:</strong>
                    <ul>
                        <li><strong>Handler:</strong> <code>https://zenscribe-bitrix24-production.up.railway.app/index.php</code></li>
                        <li><strong>Instalação:</strong> <code>https://zenscribe-bitrix24-production.up.railway.app/install.php</code></li>
                    </ul>
                </li>
                <li><strong>Defina permissões:</strong> CRM, user, profile</li>
                <li><strong>Instale o app</strong> e copie Client ID/Secret</li>
                <li><strong>Configure credenciais</strong> nas configurações do ZenScribe</li>
            </ol>
        </div>
        
        <div style="margin-top: 40px;">
            <a href="index.php" class="btn">🏠 Ir para Dashboard</a>
            <a href="install.php" class="btn">⚙️ Instalação</a>
        </div>
        
        <div style="margin-top: 40px; font-size: 12px; color: #999;">
            ZenScribe v2.0.0 - Transformando reuniões em insights acionáveis com IA
        </div>
    </div>
</body>
</html>
