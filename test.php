<?php
/**
 * ZenScribe - Página de Testes
 * Interface para executar testes de simulação
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/settings.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - Testes de Simulação</title>
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
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .test-section h3 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .test-section p {
            margin-bottom: 15px;
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
        .btn.warning {
            background: #ffc107;
            color: #212529;
        }
        .btn.warning:hover {
            background: #e0a800;
        }
        .btn.info {
            background: #17a2b8;
        }
        .btn.info:hover {
            background: #138496;
        }
        .result-area {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            min-height: 100px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            white-space: pre-wrap;
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
        .config-status {
            background: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .loading {
            color: #007bff;
            font-weight: bold;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 ZenScribe - Testes de Simulação</h1>
            <div>Teste o fluxo completo com dados simulados</div>
        </div>
        
        <div class="content">
            <!-- Status das Configurações -->
            <div class="config-status">
                <h3>📊 Status das Configurações</h3>
                <?php $config = getZenScribeConfig(); ?>
                
                <div>
                    <span>Google APIs:</span>
                    <span class="status-indicator <?= !empty($config['google']['client_id']) ? 'ok' : 'missing' ?>">
                        <?= !empty($config['google']['client_id']) ? '✅ Configurado' : '❌ Não configurado' ?>
                    </span>
                </div>
                
                <div>
                    <span>OpenAI:</span>
                    <span class="status-indicator <?= !empty($config['openai']['api_key']) ? 'ok' : 'missing' ?>">
                        <?= !empty($config['openai']['api_key']) ? '✅ Configurado' : '❌ Não configurado' ?>
                    </span>
                </div>
                
                <div>
                    <span>Processamento IA:</span>
                    <span class="status-indicator <?= $config['openai']['enabled'] ? 'ok' : 'missing' ?>">
                        <?= $config['openai']['enabled'] ? '✅ Habilitado' : '⚠️ Desabilitado' ?>
                    </span>
                </div>
            </div>
            
            <!-- Teste Completo -->
            <div class="test-section">
                <h3>🚀 Teste Completo - Simulação End-to-End</h3>
                <p>Executa todo o fluxo: reunião simulada → extração IA → detecção entidade → atualização Bitrix24 → atividade rica → agendamento</p>
                
                <button class="btn success" onclick="runFullSimulation()">
                    🎯 Executar Simulação Completa
                </button>
                
                <div id="full-result" class="result-area" style="display:none;">
                    Resultado aparecerá aqui...
                </div>
            </div>
            
            <!-- Testes Individuais -->
            <div class="test-section">
                <h3>🔬 Testes Individuais</h3>
                <p>Teste componentes específicos do sistema separadamente</p>
                
                <button class="btn info" onclick="testOpenAI()">
                    🤖 Testar OpenAI
                </button>
                
                <button class="btn warning" onclick="testBitrix()">
                    🔗 Testar Bitrix24
                </button>
                
                <button class="btn" onclick="clearResults()">
                    🗑️ Limpar Resultados
                </button>
                
                <div id="individual-result" class="result-area" style="display:none;">
                    Resultado aparecerá aqui...
                </div>
            </div>
            
            <!-- Cenários de Teste -->
            <div class="test-section">
                <h3>📋 Cenários Incluídos na Simulação</h3>
                <ul style="list-style-type: none; padding-left: 0;">
                    <li>✅ <strong>Reunião comercial</strong> com proposta de e-commerce</li>
                    <li>✅ <strong>Detecção automática</strong> de entidade Bitrix24 na descrição</li>
                    <li>✅ <strong>Transcrição realista</strong> com dores, desejos e orçamento</li>
                    <li>✅ <strong>Processamento IA</strong> via OpenAI (se configurado)</li>
                    <li>✅ <strong>Extração estruturada</strong> de dados comerciais</li>
                    <li>✅ <strong>Simulação de updates</strong> no CRM</li>
                    <li>✅ <strong>Criação de atividades</strong> ricas com contexto</li>
                    <li>✅ <strong>Agendamento automático</strong> de follow-ups</li>
                </ul>
            </div>
            
            <!-- Configurações Rápidas -->
            <div style="text-align: center; margin-top: 30px;">
                <a href="config.php" class="btn">⚙️ Configurações</a>
                <a href="index.php" class="btn">🏠 Dashboard</a>
                <a href="https://zenscribe-bitrix24-production.up.railway.app/" class="btn info">🌐 App Principal</a>
            </div>
        </div>
    </div>
    
    <script>
        async function runFullSimulation() {
            const resultDiv = document.getElementById('full-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<span class="loading">⏳ Executando simulação completa...</span>';
            
            try {
                const response = await fetch('test-simulation.php?action=run_simulation', {
                    method: 'GET'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `<span class="success">✅ ${result.message}</span>\n\n` +
                        JSON.stringify(result.data, null, 2);
                } else {
                    resultDiv.innerHTML = `<span class="error">❌ ${result.message}</span>\n\n` +
                        JSON.stringify(result, null, 2);
                }
            } catch (error) {
                resultDiv.innerHTML = `<span class="error">❌ Erro: ${error.message}</span>`;
            }
        }
        
        async function testOpenAI() {
            const resultDiv = document.getElementById('individual-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<span class="loading">⏳ Testando OpenAI...</span>';
            
            try {
                const response = await fetch('test-simulation.php?action=test_openai_only', {
                    method: 'GET'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `<span class="success">✅ ${result.message}</span>\n\n` +
                        JSON.stringify(result.data, null, 2);
                } else {
                    resultDiv.innerHTML = `<span class="error">❌ ${result.message}</span>\n\n` +
                        JSON.stringify(result, null, 2);
                }
            } catch (error) {
                resultDiv.innerHTML = `<span class="error">❌ Erro: ${error.message}</span>`;
            }
        }
        
        async function testBitrix() {
            const resultDiv = document.getElementById('individual-result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<span class="loading">⏳ Testando Bitrix24...</span>';
            
            try {
                const response = await fetch('test-simulation.php?action=test_bitrix_only', {
                    method: 'GET'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `<span class="success">✅ ${result.message}</span>\n\n` +
                        JSON.stringify(result.data, null, 2);
                } else {
                    resultDiv.innerHTML = `<span class="error">❌ ${result.message}</span>\n\n` +
                        JSON.stringify(result, null, 2);
                }
            } catch (error) {
                resultDiv.innerHTML = `<span class="error">❌ Erro: ${error.message}</span>`;
            }
        }
        
        function clearResults() {
            document.getElementById('full-result').style.display = 'none';
            document.getElementById('individual-result').style.display = 'none';
        }
    </script>
</body>
</html>