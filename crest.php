<?php
/**
 * ZenScribe - Bitrix24 API Client
 * Baseado no CRest oficial
 */

define('C_REST_CLIENT_ID', 'local.68c38a4fdd55e8.90030602'); // Client ID do Bitrix24
define('C_REST_CLIENT_SECRET', '4SJC7Xon7yY6xSF0WQCQAZKPBNp5JNNk8b720g7OrISVFE39zo'); // Client Secret do Bitrix24
define('C_REST_CURRENT_ENCODING', 'UTF-8');
define('C_REST_IGNORE_SSL', true);
define('C_REST_LOGS_DIR', __DIR__ . '/logs/');

class CRest
{
    const VERSION = '1.36';
    const BATCH_COUNT = 50; // max count batch 50
    
    protected static $applicationId = '';
    protected static $applicationSecret = '';
    protected static $accessToken = '';
    protected static $refreshToken = '';
    protected static $endPoint = 'https://oauth.bitrix.info/oauth/token/';
    protected static $scope = 'crm,user,calendar';
    protected static $userId = null;
    protected static $domain = '';
    
    public static function installApp()
    {
        if (!isset($_REQUEST['code'])) {
            $result['error'] = 'unknown_error';
            $result['error_description'] = 'Wrong install request';
        } else {
            $result = static::getInstallToken($_REQUEST['code'], $_REQUEST['domain'], $_REQUEST['member_id']);
            if (!isset($result['error'])) {
                if (static::setAppSettings($result, true)) {
                    $result['install'] = 'success';
                } else {
                    $result['error'] = 'unknown_error';
                    $result['error_description'] = 'Error install app settings';
                }
            }
        }
        $result['rest_only'] = true;
        return $result;
    }
    
    public static function call($method, $params = [])
    {
        if (!static::checkSettings()) {
            $result = [
                'error' => 'settings_error',
                'error_description' => 'ZenScribe app not configured'
            ];
            return $result;
        }
        
        $url = 'https://' . static::$domain . '/rest/' . $method . '.json';
        
        if (static::$accessToken) {
            $params['auth'] = static::$accessToken;
        }
        
        $result = static::request($url, $params);
        
        if (isset($result['error']) && $result['error'] === 'expired_token') {
            $result = static::refreshToken();
            if (!isset($result['error'])) {
                $params['auth'] = static::$accessToken;
                $result = static::request($url, $params);
            }
        }
        
        static::writeToLog(['method' => $method, 'params' => $params, 'result' => $result], 'call');
        
        return $result;
    }
    
    protected static function request($url, $params)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        
        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            return [
                'error' => 'network_error',
                'error_description' => "HTTP $httpCode"
            ];
        }
        
        return json_decode($result, true);
    }
    
    protected static function getInstallToken($code, $domain, $memberId)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'client_id' => C_REST_CLIENT_ID,
            'client_secret' => C_REST_CLIENT_SECRET,
            'code' => $code,
            'scope' => static::$scope
        ];
        
        return static::request(static::$endPoint, $params);
    }
    
    protected static function refreshToken()
    {
        $params = [
            'grant_type' => 'refresh_token',
            'client_id' => C_REST_CLIENT_ID,
            'client_secret' => C_REST_CLIENT_SECRET,
            'refresh_token' => static::$refreshToken
        ];
        
        $result = static::request(static::$endPoint, $params);
        
        if (!isset($result['error'])) {
            static::setAppSettings($result);
        }
        
        return $result;
    }
    
    protected static function setAppSettings($settings, $isInstall = false)
    {
        if (!file_exists(__DIR__ . '/settings.json')) {
            $config = [];
        } else {
            $config = json_decode(file_get_contents(__DIR__ . '/settings.json'), true);
        }
        
        $config['access_token'] = $settings['access_token'];
        $config['refresh_token'] = $settings['refresh_token'];
        $config['domain'] = $settings['domain'];
        $config['user_id'] = $settings['user_id'];
        $config['scope'] = $settings['scope'];
        
        if ($isInstall) {
            $config['client_id'] = C_REST_CLIENT_ID;
            $config['client_secret'] = C_REST_CLIENT_SECRET;
            $config['installed_at'] = date('Y-m-d H:i:s');
        }
        
        $result = file_put_contents(__DIR__ . '/settings.json', json_encode($config, JSON_PRETTY_PRINT));
        
        if ($result) {
            static::$accessToken = $settings['access_token'];
            static::$refreshToken = $settings['refresh_token'];
            static::$domain = $settings['domain'];
            static::$userId = $settings['user_id'];
        }
        
        return $result;
    }
    
    protected static function checkSettings()
    {
        if (empty(static::$accessToken)) {
            static::getSettings();
        }
        
        return !empty(static::$accessToken) && !empty(static::$domain);
    }
    
    protected static function getSettings()
    {
        if (file_exists(__DIR__ . '/settings.json')) {
            $config = json_decode(file_get_contents(__DIR__ . '/settings.json'), true);
            
            static::$accessToken = $config['access_token'] ?? '';
            static::$refreshToken = $config['refresh_token'] ?? '';
            static::$domain = $config['domain'] ?? '';
            static::$userId = $config['user_id'] ?? '';
        }
    }
    
    protected static function writeToLog($data, $type = 'log')
    {
        if (!defined('C_REST_BLOCK_LOG') || C_REST_BLOCK_LOG !== true) {
            $dir = C_REST_LOGS_DIR;
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $file = $dir . '/zenscribe_' . date('Y-m-d') . '.log';
            $logEntry = date('[Y-m-d H:i:s] ') . $type . ': ' . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
            file_put_contents($file, $logEntry, FILE_APPEND | LOCK_EX);
        }
    }
}
?>
