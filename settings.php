<?php
/**
 * ZenScribe App - Configuration
 * Configurações do app local Bitrix24
 */

// Inicializar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações da aplicação (preencher ao criar app no Bitrix24)
define('ZENSCRIBE_CLIENT_ID', 'local.673d4c5d12345.67890123');  // App ID do Bitrix24
define('ZENSCRIBE_CLIENT_SECRET', 'AbCdEfGhIjKlMnOpQrStUvWxYz1234567890'); // App Secret

// Configurações Google OAuth
define('GOOGLE_CLIENT_ID', '');     // Será configurado pelo usuário
define('GOOGLE_CLIENT_SECRET', ''); // Será configurado pelo usuário
define('GOOGLE_REDIRECT_URI', 'https://chromiumapp.org/');

// Configurações OpenAI
define('OPENAI_API_KEY', '');      // Será configurado pelo usuário
define('OPENAI_MODEL', 'gpt-4o-mini');

// Configurações da aplicação
define('ZENSCRIBE_VERSION', '2.0.0');
define('ZENSCRIBE_NAME', 'ZenScribe - AI Meeting Processor');

// Logs e debug
define('ENABLE_LOGS', true);
define('LOG_LEVEL', 'info'); // debug, info, warn, error

// Diretórios
define('LOGS_DIR', __DIR__ . '/logs/');
define('TEMP_DIR', __DIR__ . '/temp/');

// Configurações de segurança
define('MAX_PROCESSING_TIME', 300); // 5 minutos
define('MAX_TRANSCRIPT_SIZE', 100000); // 100KB

/**
 * Obtém configuração específica do usuário/portal
 */
function getZenScribeConfig($key = null) {
    // Verificar se existe na sessão
    if (isset($_SESSION['zenscribe_config'])) {
        $config = $_SESSION['zenscribe_config'];
        return $key ? $config[$key] ?? null : $config;
    }
    
    // Configuração padrão se não existir
    $defaultConfig = [
        'google' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => GOOGLE_REDIRECT_URI
        ],
        'openai' => [
            'api_key' => '',
            'model' => OPENAI_MODEL,
            'enabled' => false
        ],
        'processing' => [
            'auto_scheduling' => true,
            'auto_contact_creation' => true,
            'default_entity' => 'lead'
        ],
        'bitrix' => [
            'default_responsible_id' => 1,
            'activity_type' => 'ZENSCRIBE_MEETING'
        ]
    ];
    
    return $key ? $defaultConfig[$key] ?? null : $defaultConfig;
}

/**
 * Salva configuração específica
 */
function saveZenScribeConfig($config) {
    // Usar sessão ao invés de arquivo devido às limitações do Railway
    $_SESSION['zenscribe_config'] = $config;
    return true;
}

/**
 * Log helper
 */
function zenLog($message, $level = 'info', $context = []) {
    if (!ENABLE_LOGS) return;
    
    $dir = LOGS_DIR;
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'message' => $message,
        'context' => $context
    ];
    
    $file = $dir . 'zenscribe_' . date('Y-m-d') . '.log';
    file_put_contents($file, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * Error handler personalizado
 */
function zenError($message, $context = [], $httpCode = 500) {
    zenLog($message, 'error', $context);
    
    http_response_code($httpCode);
    
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $message,
        'timestamp' => date('c'),
        'context' => $context
    ]);
    exit;
}

/**
 * Success response helper
 */
function zenSuccess($data = [], $message = 'Success') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ]);
}
?>
