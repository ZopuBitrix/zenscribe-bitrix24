<?php
/**
 * ZenScribe App - API Endpoints
 * Endpoints para estatísticas, logs e dados
 */

require_once(__DIR__ . '/crest.php');
require_once(__DIR__ . '/settings.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'stats':
        getStats();
        break;
    case 'recent_activities':
        getRecentActivities();
        break;
    case 'export_logs':
        exportLogs();
        break;
    case 'export_config':
        exportConfig();
        break;
    case 'health_check':
        healthCheck();
        break;
    default:
        zenError('Endpoint não encontrado', ['action' => $action], 404);
}

/**
 * Retorna estatísticas de uso
 */
function getStats() {
    try {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        
        // Contar processamentos nos logs
        $meetingsToday = countLogEntries($today, 'Processamento concluído');
        $meetingsMonth = countLogEntries($thisMonth, 'Processamento concluído', 'month');
        
        // Calcular taxa de sucesso
        $totalProcessing = countLogEntries($thisMonth, 'processamento', 'month');
        $successfulProcessing = countLogEntries($thisMonth, 'Processamento concluído', 'month');
        
        $successRate = $totalProcessing > 0 ? round(($successfulProcessing / $totalProcessing) * 100) : 100;
        
        // Estatísticas do Bitrix24
        $bitrixStats = getBitrixStats();
        
        zenSuccess([
            'today' => $meetingsToday,
            'month' => $meetingsMonth,
            'success_rate' => $successRate,
            'total_leads_created' => $bitrixStats['leads_created'] ?? 0,
            'total_deals_updated' => $bitrixStats['deals_updated'] ?? 0,
            'last_processing' => getLastProcessingTime()
        ]);
        
    } catch (Exception $e) {
        zenError('Erro ao obter estatísticas: ' . $e->getMessage());
    }
}

/**
 * Retorna atividades recentes
 */
function getRecentActivities() {
    try {
        $activities = [];
        
        // Buscar dos logs
        $logActivities = getLogActivities();
        
        // Buscar do Bitrix24
        $bitrixActivities = getBitrixActivities();
        
        // Combinar e ordenar
        $allActivities = array_merge($logActivities, $bitrixActivities);
        usort($allActivities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        // Limitar a 10 mais recentes
        $recentActivities = array_slice($allActivities, 0, 10);
        
        zenSuccess(array_map(function($activity) {
            return [
                'title' => $activity['title'],
                'time' => date('d/m H:i', strtotime($activity['timestamp'])),
                'type' => $activity['type'] ?? 'info'
            ];
        }, $recentActivities));
        
    } catch (Exception $e) {
        zenError('Erro ao obter atividades: ' . $e->getMessage());
    }
}

/**
 * Exporta logs para download
 */
function exportLogs() {
    try {
        $logDir = LOGS_DIR;
        $zipFile = TEMP_DIR . 'zenscribe_logs_' . date('Y-m-d_H-i-s') . '.zip';
        
        // Criar diretório temp se não existir
        if (!file_exists(TEMP_DIR)) {
            mkdir(TEMP_DIR, 0755, true);
        }
        
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
                
                // Adicionar logs dos últimos 7 dias
                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $logFile = $logDir . "zenscribe_$date.log";
                    
                    if (file_exists($logFile)) {
                        $zip->addFile($logFile, "zenscribe_$date.log");
                    }
                }
                
                // Adicionar arquivo de configuração (sem senhas)
                $config = getZenScribeConfig();
                unset($config['google']['client_secret']);
                unset($config['openai']['api_key']);
                
                $zip->addFromString('config_export.json', json_encode($config, JSON_PRETTY_PRINT));
                
                $zip->close();
                
                // Download
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="zenscribe_logs.zip"');
                header('Content-Length: ' . filesize($zipFile));
                readfile($zipFile);
                
                // Limpar arquivo temporário
                unlink($zipFile);
                exit;
            }
        }
        
        // Fallback: exportar apenas último log
        $lastLog = $logDir . 'zenscribe_' . date('Y-m-d') . '.log';
        if (file_exists($lastLog)) {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="zenscribe_' . date('Y-m-d') . '.log"');
            readfile($lastLog);
            exit;
        }
        
        zenError('Nenhum log encontrado para exportar');
        
    } catch (Exception $e) {
        zenError('Erro ao exportar logs: ' . $e->getMessage());
    }
}

/**
 * Exporta configuração
 */
function exportConfig() {
    try {
        $config = getZenScribeConfig();
        
        // Remover informações sensíveis
        unset($config['google']['client_secret']);
        unset($config['openai']['api_key']);
        
        $exportData = [
            'zenscribe_version' => ZENSCRIBE_VERSION,
            'export_date' => date('c'),
            'config' => $config
        ];
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="zenscribe_config_' . date('Y-m-d') . '.json"');
        echo json_encode($exportData, JSON_PRETTY_PRINT);
        exit;
        
    } catch (Exception $e) {
        zenError('Erro ao exportar configuração: ' . $e->getMessage());
    }
}

/**
 * Health check do sistema
 */
function healthCheck() {
    $checks = [];
    
    // Verificar Bitrix24
    try {
        $result = CRest::call('profile');
        $checks['bitrix24'] = [
            'status' => !isset($result['error']),
            'message' => isset($result['error']) ? $result['error_description'] : 'Conectado'
        ];
    } catch (Exception $e) {
        $checks['bitrix24'] = [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
    
    // Verificar configurações
    $config = getZenScribeConfig();
    $checks['google_config'] = [
        'status' => !empty($config['google']['client_id']),
        'message' => !empty($config['google']['client_id']) ? 'Configurado' : 'Client ID não configurado'
    ];
    
    $checks['openai_config'] = [
        'status' => !empty($config['openai']['api_key']),
        'message' => !empty($config['openai']['api_key']) ? 'Configurado' : 'API Key não configurado'
    ];
    
    // Verificar diretórios
    $checks['logs_dir'] = [
        'status' => is_writable(LOGS_DIR),
        'message' => is_writable(LOGS_DIR) ? 'Gravável' : 'Não gravável'
    ];
    
    $checks['temp_dir'] = [
        'status' => is_writable(TEMP_DIR),
        'message' => is_writable(TEMP_DIR) ? 'Gravável' : 'Não gravável'
    ];
    
    // Status geral
    $allHealthy = true;
    foreach ($checks as $check) {
        if (!$check['status']) {
            $allHealthy = false;
            break;
        }
    }
    
    zenSuccess([
        'overall_status' => $allHealthy,
        'checks' => $checks,
        'timestamp' => date('c')
    ]);
}

/**
 * Helper: Contar entradas nos logs
 */
function countLogEntries($date, $searchTerm, $mode = 'day') {
    $count = 0;
    $logDir = LOGS_DIR;
    
    if ($mode === 'month') {
        // Buscar todos os dias do mês
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayStr = sprintf('%02d', $day);
            $logFile = $logDir . "zenscribe_{$year}-{$month}-{$dayStr}.log";
            
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                $count += substr_count(strtolower($content), strtolower($searchTerm));
            }
        }
    } else {
        // Buscar apenas o dia específico
        $logFile = $logDir . "zenscribe_$date.log";
        
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $count = substr_count(strtolower($content), strtolower($searchTerm));
        }
    }
    
    return $count;
}

/**
 * Helper: Obter estatísticas do Bitrix24
 */
function getBitrixStats() {
    try {
        // Buscar atividades criadas pelo ZenScribe
        $result = CRest::call('crm.activity.list', [
            'filter' => [
                'SUBJECT' => '%ZenScribe%'
            ],
            'select' => ['ID', 'CREATED', 'OWNER_TYPE_ID']
        ]);
        
        if (isset($result['result'])) {
            $activities = $result['result'];
            $stats = [
                'total_activities' => count($activities),
                'leads_created' => 0,
                'deals_updated' => 0
            ];
            
            foreach ($activities as $activity) {
                if ($activity['OWNER_TYPE_ID'] == 1) { // Lead
                    $stats['leads_created']++;
                } elseif ($activity['OWNER_TYPE_ID'] == 2) { // Deal
                    $stats['deals_updated']++;
                }
            }
            
            return $stats;
        }
    } catch (Exception $e) {
        zenLog('Erro ao obter stats Bitrix24', 'warn', ['error' => $e->getMessage()]);
    }
    
    return [];
}

/**
 * Helper: Obter atividades dos logs
 */
function getLogActivities() {
    $activities = [];
    $logFile = LOGS_DIR . 'zenscribe_' . date('Y-m-d') . '.log';
    
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        
        foreach (array_reverse($lines) as $line) {
            $logEntry = json_decode($line, true);
            
            if ($logEntry && strpos($logEntry['message'], 'concluído') !== false) {
                $activities[] = [
                    'title' => '🎯 ' . $logEntry['message'],
                    'timestamp' => $logEntry['timestamp'],
                    'type' => 'success'
                ];
                
                if (count($activities) >= 5) break; // Limitar a 5 do log
            }
        }
    }
    
    return $activities;
}

/**
 * Helper: Obter atividades do Bitrix24
 */
function getBitrixActivities() {
    $activities = [];
    
    try {
        $result = CRest::call('crm.activity.list', [
            'filter' => [
                'SUBJECT' => '%ZenScribe%',
                '>=CREATED' => date('Y-m-d 00:00:00', strtotime('-7 days'))
            ],
            'select' => ['ID', 'SUBJECT', 'CREATED'],
            'order' => ['CREATED' => 'DESC']
        ]);
        
        if (isset($result['result'])) {
            foreach ($result['result'] as $activity) {
                $activities[] = [
                    'title' => $activity['SUBJECT'],
                    'timestamp' => $activity['CREATED'],
                    'type' => 'bitrix'
                ];
            }
        }
    } catch (Exception $e) {
        zenLog('Erro ao obter atividades Bitrix24', 'warn', ['error' => $e->getMessage()]);
    }
    
    return array_slice($activities, 0, 5); // Limitar a 5 do Bitrix24
}

/**
 * Helper: Obter horário do último processamento
 */
function getLastProcessingTime() {
    $logFile = LOGS_DIR . 'zenscribe_' . date('Y-m-d') . '.log';
    
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        
        foreach (array_reverse($lines) as $line) {
            $logEntry = json_decode($line, true);
            
            if ($logEntry && strpos($logEntry['message'], 'Processamento concluído') !== false) {
                return $logEntry['timestamp'];
            }
        }
    }
    
    return null;
}
?>
