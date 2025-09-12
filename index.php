<?php
/**
 * ZenScribe App - Main Interface
 * Interface principal dentro do Bitrix24
 */

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

// Verificar se app está instalado

// Carregar configurações do usuário
$config = getZenScribeConfig();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ZenScribe - AI Meeting Processor</title>
    <script src="//api.bitrix24.com/api/v1/"></script>
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
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .header .subtitle {
            opacity: 0.9;
            font-size: 1.1em;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        .card h3 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 1.3em;
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
        .btn.secondary {
            background: #6c757d;
        }
        .btn.secondary:hover {
            background: #545b62;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .config-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .config-item:last-child {
            border-bottom: none;
        }
        .config-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .config-status.ok {
            background: #d4edda;
            color: #155724;
        }
        .config-status.missing {
            background: #f8d7da;
            color: #721c24;
        }
        .logs {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
        .recent-activities {
            max-height: 300px;
            overflow-y: auto;
        }
        .activity-item {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-time {
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <script>
        BX24.init(function(){
            console.log('🎯 ZenScribe interface carregada');
        });
    </script>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎯 ZenScribe</h1>
            <div class="subtitle">AI-Powered Meeting Processor para Bitrix24</div>
        </div>

        <!-- Cards principais -->
        <div class="cards">
            <!-- Card: Processar Reunião -->
            <div class="card">
                <h3>🚀 Processar Reunião</h3>
                <p>Analise sua última reunião e atualize automaticamente o CRM com dados extraídos por IA.</p>
                
                <div id="processing-status" class="status info" style="display: none;">
                    ⏳ Processando reunião...
                </div>
                
                <button class="btn success" onclick="processLatestMeeting()">
                    🎯 Processar Última Reunião
                </button>
                
                <button class="btn secondary" onclick="showMeetingHistory()">
                    📋 Histórico
                </button>
            </div>

            <!-- Card: Status das Configurações -->
            <div class="card">
                <h3>⚙️ Status das Configurações</h3>
                
                <div class="config-item">
                    <span>Google APIs (Calendar/Drive)</span>
                    <span class="config-status <?= empty($config['google']['client_id']) ? 'missing' : 'ok' ?>">
                        <?= empty($config['google']['client_id']) ? '❌ Não configurado' : '✅ Configurado' ?>
                    </span>
                </div>
                
                <div class="config-item">
                    <span>OpenAI (Processamento IA)</span>
                    <span class="config-status <?= empty($config['openai']['api_key']) ? 'missing' : 'ok' ?>">
                        <?= empty($config['openai']['api_key']) ? '❌ Não configurado' : '✅ Configurado' ?>
                    </span>
                </div>
                
                <div class="config-item">
                    <span>Bitrix24 App</span>
                    <span class="config-status ok">✅ Instalado</span>
                </div>
                
                <button class="btn" onclick="openConfiguration()">
                    ⚙️ Configurar Credenciais
                </button>
            </div>

            <!-- Card: Estatísticas -->
            <div class="card">
                <h3>📊 Estatísticas</h3>
                
                <div class="config-item">
                    <span>Reuniões processadas hoje</span>
                    <span><strong id="meetings-today">0</strong></span>
                </div>
                
                <div class="config-item">
                    <span>Total este mês</span>
                    <span><strong id="meetings-month">0</strong></span>
                </div>
                
                <div class="config-item">
                    <span>Taxa de sucesso</span>
                    <span><strong id="success-rate">100%</strong></span>
                </div>
                
                <button class="btn secondary" onclick="exportLogs()">
                    📁 Exportar Logs
                </button>
            </div>
        </div>

        <!-- Atividades Recentes -->
        <div class="card">
            <h3>📋 Atividades Recentes</h3>
            <div class="recent-activities" id="recent-activities">
                <div class="activity-item">
                    <div>Carregando atividades...</div>
                    <div class="activity-time">aguarde</div>
                </div>
            </div>
        </div>

        <!-- Logs (apenas para debug) -->
        <?php if (LOG_LEVEL === 'debug'): ?>
        <div class="card">
            <h3>🔧 Debug Logs</h3>
            <div class="logs" id="debug-logs">
                Logs aparecerão aqui durante o processamento...
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Funções principais da interface
        async function processLatestMeeting() {
            const statusDiv = document.getElementById('processing-status');
            statusDiv.style.display = 'block';
            statusDiv.className = 'status info';
            statusDiv.innerHTML = '⏳ Buscando última reunião...';
            
            try {
                const response = await fetch('handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'process_latest_meeting'
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    statusDiv.className = 'status success';
                    statusDiv.innerHTML = '✅ ' + result.message;
                    
                    // Atualizar estatísticas
                    updateStats();
                    loadRecentActivities();
                } else {
                    statusDiv.className = 'status error';
                    statusDiv.innerHTML = '❌ Erro: ' + result.message;
                }
                
            } catch (error) {
                statusDiv.className = 'status error';
                statusDiv.innerHTML = '❌ Erro de conexão: ' + error.message;
            }
        }
        
        function openConfiguration() {
            window.open('config.php', '_blank', 'width=800,height=600');
        }
        
        function showMeetingHistory() {
            BX24.openPath('/crm/activity/');
        }
        
        async function updateStats() {
            try {
                const response = await fetch('api.php?action=stats');
                const stats = await response.json();
                
                if (stats.success) {
                    document.getElementById('meetings-today').textContent = stats.data.today || 0;
                    document.getElementById('meetings-month').textContent = stats.data.month || 0;
                    document.getElementById('success-rate').textContent = (stats.data.success_rate || 100) + '%';
                }
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        }
        
        async function loadRecentActivities() {
            try {
                const response = await fetch('api.php?action=recent_activities');
                const activities = await response.json();
                
                const container = document.getElementById('recent-activities');
                
                if (activities.success && activities.data.length > 0) {
                    container.innerHTML = activities.data.map(activity => `
                        <div class="activity-item">
                            <div>${activity.title}</div>
                            <div class="activity-time">${activity.time}</div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="activity-item">Nenhuma atividade recente</div>';
                }
            } catch (error) {
                console.error('Erro ao carregar atividades:', error);
            }
        }
        
        function exportLogs() {
            window.open('api.php?action=export_logs', '_blank');
        }
        
        // Carregar dados iniciais
        document.addEventListener('DOMContentLoaded', function() {
            updateStats();
            loadRecentActivities();
        });
        
        // Auto-refresh a cada 30 segundos
        setInterval(function() {
            updateStats();
            loadRecentActivities();
        }, 30000);
    </script>
</body>
</html>
