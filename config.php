<?php
/**
 * ZenScribe App - Configuration Interface
 * Interface para configurar credenciais e opções
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config = getZenScribeConfig();
    
    // Atualizar Google
    if (isset($_POST['google_client_id'])) {
        $config['google']['client_id'] = trim($_POST['google_client_id']);
        $config['google']['client_secret'] = trim($_POST['google_client_secret']);
    }
    
    // Atualizar OpenAI
    if (isset($_POST['openai_api_key'])) {
        $config['openai']['api_key'] = trim($_POST['openai_api_key']);
        $config['openai']['enabled'] = isset($_POST['openai_enabled']);
    }
    
    // Atualizar configurações gerais
    if (isset($_POST['auto_scheduling'])) {
        $config['processing']['auto_scheduling'] = isset($_POST['auto_scheduling']);
        $config['processing']['auto_contact_creation'] = isset($_POST['auto_contact_creation']);
        $config['processing']['default_entity'] = $_POST['default_entity'] ?? 'lead';
    }
    
    if (saveZenScribeConfig($config)) {
        $success = "Configurações salvas com sucesso!";
    } else {
        $error = "Erro ao salvar configurações";
    }
}

$config = getZenScribeConfig();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - Configurações</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid #e9ecef;
        }
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .section h2 {
            color: #495057;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
        }
        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
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
            margin: 5px 5px 5px 0;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn.success {
            background: #28a745;
        }
        .btn.success:hover {
            background: #1e7e34;
        }
        .btn.secondary {
            background: #6c757d;
        }
        .btn.secondary:hover {
            background: #545b62;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-indicator {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }
        .status-indicator.ok {
            background: #d4edda;
            color: #155724;
        }
        .status-indicator.missing {
            background: #f8d7da;
            color: #721c24;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Configurações ZenScribe</h1>
            <div>Configure suas credenciais e preferências</div>
        </div>
        
        <div class="content">
            <?php if (isset($success)): ?>
            <div class="alert success">✅ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <!-- Google APIs -->
                <div class="section">
                    <h2>🔗 Google APIs</h2>
                    <p>Configure suas credenciais do Google Cloud Console para acessar Calendar e Drive.</p>
                    
                    <div class="form-group">
                        <label>Client ID:</label>
                        <input type="text" name="google_client_id" 
                               value="<?= htmlspecialchars($config['google']['client_id']) ?>"
                               placeholder="236047145381-xxxxx.apps.googleusercontent.com">
                        <div class="help-text">Obtido no Google Cloud Console > APIs & Services > Credentials</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Client Secret:</label>
                        <input type="password" name="google_client_secret" 
                               value="<?= htmlspecialchars($config['google']['client_secret']) ?>"
                               placeholder="GOCSPX-xxxxxxxxxxxxxxxxxxxxxxxx">
                        <div class="help-text">Secret correspondente ao Client ID</div>
                    </div>
                    
                    <div class="help-text">
                        <strong>📋 APIs necessárias:</strong><br>
                        • Google Calendar API<br>
                        • Google Drive API<br>
                        • Google Docs API
                    </div>
                    
                    <button type="button" class="btn secondary" onclick="testGoogleConnection()">
                        🔍 Testar Conexão Google
                    </button>
                    <div id="google-test-result"></div>
                </div>
                
                <!-- OpenAI -->
                <div class="section">
                    <h2>🤖 OpenAI (Opcional)</h2>
                    <p>Configure para processamento inteligente com IA. Sem OpenAI, usará heurísticas básicas.</p>
                    
                    <div class="form-group">
                        <label>API Key:</label>
                        <input type="password" name="openai_api_key" 
                               value="<?= htmlspecialchars($config['openai']['api_key']) ?>"
                               placeholder="sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx">
                        <div class="help-text">Obtida em platform.openai.com > API Keys</div>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="openai_enabled" id="openai_enabled"
                               <?= $config['openai']['enabled'] ? 'checked' : '' ?>>
                        <label for="openai_enabled">Usar OpenAI para extração inteligente</label>
                    </div>
                    
                    <button type="button" class="btn secondary" onclick="testOpenAI()">
                        🧠 Testar OpenAI
                    </button>
                    <div id="openai-test-result"></div>
                </div>
                
                <!-- Configurações de Processamento -->
                <div class="section">
                    <h2>🎯 Configurações de Processamento</h2>
                    
                    <div class="two-columns">
                        <div>
                            <div class="form-group">
                                <label>Entidade padrão (quando não detectada):</label>
                                <select name="default_entity">
                                    <option value="lead" <?= $config['processing']['default_entity'] === 'lead' ? 'selected' : '' ?>>Lead</option>
                                    <option value="deal" <?= $config['processing']['default_entity'] === 'deal' ? 'selected' : '' ?>>Deal</option>
                                    <option value="contact" <?= $config['processing']['default_entity'] === 'contact' ? 'selected' : '' ?>>Contact</option>
                                    <option value="company" <?= $config['processing']['default_entity'] === 'company' ? 'selected' : '' ?>>Company</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <div class="checkbox-group">
                                <input type="checkbox" name="auto_scheduling" id="auto_scheduling"
                                       <?= $config['processing']['auto_scheduling'] ? 'checked' : '' ?>>
                                <label for="auto_scheduling">Agendamento automático de follow-ups</label>
                            </div>
                            
                            <div class="checkbox-group">
                                <input type="checkbox" name="auto_contact_creation" id="auto_contact_creation"
                                       <?= $config['processing']['auto_contact_creation'] ? 'checked' : '' ?>>
                                <label for="auto_contact_creation">Criar contatos automaticamente</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Atual -->
                <div class="section">
                    <h2>📊 Status Atual</h2>
                    
                    <div class="config-item">
                        <span>Google APIs:</span>
                        <span class="status-indicator <?= !empty($config['google']['client_id']) ? 'ok' : 'missing' ?>">
                            <?= !empty($config['google']['client_id']) ? '✅ Configurado' : '❌ Não configurado' ?>
                        </span>
                    </div>
                    
                    <div class="config-item">
                        <span>OpenAI:</span>
                        <span class="status-indicator <?= !empty($config['openai']['api_key']) ? 'ok' : 'missing' ?>">
                            <?= !empty($config['openai']['api_key']) ? '✅ Configurado' : '❌ Não configurado' ?>
                        </span>
                    </div>
                    
                    <div class="config-item">
                        <span>Bitrix24 App:</span>
                        <span class="status-indicator ok">✅ Instalado</span>
                    </div>
                </div>
                
                <!-- Botões -->
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn success">💾 Salvar Configurações</button>
                    <button type="button" class="btn secondary" onclick="window.close()">❌ Cancelar</button>
                    <button type="button" class="btn" onclick="exportConfig()">📁 Exportar Config</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        async function testGoogleConnection() {
            const resultDiv = document.getElementById('google-test-result');
            resultDiv.innerHTML = '<div style="color: #007bff;">⏳ Testando conexão Google...</div>';
            
            try {
                const response = await fetch('handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'test_google_auth' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = '<div style="color: #28a745;">✅ ' + result.message + '</div>';
                } else {
                    resultDiv.innerHTML = '<div style="color: #dc3545;">❌ ' + result.message + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div style="color: #dc3545;">❌ Erro: ' + error.message + '</div>';
            }
        }
        
        async function testOpenAI() {
            const resultDiv = document.getElementById('openai-test-result');
            resultDiv.innerHTML = '<div style="color: #007bff;">⏳ Testando OpenAI...</div>';
            
            try {
                const response = await fetch('handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'test_openai' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = '<div style="color: #28a745;">✅ ' + result.message + '</div>';
                } else {
                    resultDiv.innerHTML = '<div style="color: #dc3545;">❌ ' + result.message + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div style="color: #dc3545;">❌ Erro: ' + error.message + '</div>';
            }
        }
        
        function exportConfig() {
            window.open('api.php?action=export_config', '_blank');
        }
    </script>
</body>
</html>
