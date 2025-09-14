<?php
/**
 * Visualizador de logs do ZenScribe
 */

// Inicializar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=UTF-8');

echo "<h2>📋 Logs do ZenScribe</h2>";

// Tentar diferentes localizações de logs
$logPaths = [
    __DIR__ . '/logs/',
    __DIR__ . '/temp/',
    __DIR__ . '/',
    sys_get_temp_dir() . '/',
    '/tmp/'
];

echo "<h3>🔍 Procurando arquivos de log...</h3>";

$foundLogs = [];

foreach ($logPaths as $path) {
    echo "<p><strong>Verificando: $path</strong></p>";
    
    if (is_dir($path)) {
        $files = glob($path . '*.log');
        if ($files) {
            echo "<ul>";
            foreach ($files as $file) {
                echo "<li>✅ " . basename($file) . " (" . date('Y-m-d H:i:s', filemtime($file)) . ")</li>";
                $foundLogs[] = $file;
            }
            echo "</ul>";
        } else {
            echo "<p>❌ Nenhum .log encontrado</p>";
        }
    } else {
        echo "<p>❌ Diretório não existe</p>";
    }
}

if (empty($foundLogs)) {
    echo "<h3>🔍 Verificando arquivos PHP para logs inline</h3>";
    
    // Verificar se há logs escritos diretamente em arquivos PHP
    $phpFiles = ['debug.php', 'debug.log', 'error.log', 'zenscribe.log'];
    
    foreach ($phpFiles as $file) {
        $filePath = __DIR__ . '/' . $file;
        if (file_exists($filePath)) {
            echo "<p>✅ Encontrado: $file</p>";
            $foundLogs[] = $filePath;
        }
    }
}

// Mostrar logs mais recentes
if (!empty($foundLogs)) {
    echo "<h3>📋 Últimos logs (mais recente primeiro)</h3>";
    
    // Ordenar por data de modificação
    usort($foundLogs, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    foreach (array_slice($foundLogs, 0, 3) as $logFile) {
        echo "<h4>📄 " . basename($logFile) . " (modificado: " . date('Y-m-d H:i:s', filemtime($logFile)) . ")</h4>";
        
        $content = file_get_contents($logFile);
        if (strlen($content) > 10000) {
            $content = "... (mostrando últimos 10KB) ...\n" . substr($content, -10000);
        }
        
        echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow: auto; font-size: 12px;'>";
        echo htmlspecialchars($content);
        echo "</pre>";
    }
} else {
    echo "<h3>⚠️ Nenhum arquivo de log encontrado</h3>";
    echo "<p>Vamos criar um teste que force a criação de logs:</p>";
    
    // Teste rápido para gerar logs
    require_once(__DIR__ . '/settings.php');
    
    echo "<h4>🧪 Teste de logging</h4>";
    try {
        zenLog('Teste de log via view-logs.php', 'info', ['timestamp' => date('Y-m-d H:i:s')]);
        echo "<p>✅ Log de teste criado</p>";
        
        // Tentar encontrar onde foi criado
        foreach ($logPaths as $path) {
            $testFiles = glob($path . '*');
            if ($testFiles) {
                echo "<p><strong>Arquivos em $path:</strong></p>";
                echo "<ul>";
                foreach (array_slice($testFiles, 0, 10) as $file) {
                    if (is_file($file)) {
                        echo "<li>" . basename($file) . " (mod: " . date('H:i:s', filemtime($file)) . ")</li>";
                    }
                }
                echo "</ul>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Erro ao criar log: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>🚀 Execute uma simulação ZenScribe agora</h3>";
echo "<p>Depois volte aqui para ver os logs gerados em tempo real.</p>";
echo "<p><a href='index.php'>🏠 Voltar ao Dashboard</a> | <a href='view-logs.php'>🔄 Atualizar Logs</a></p>";
?>
