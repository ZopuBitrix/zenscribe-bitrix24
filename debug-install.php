<?php
/**
 * Debug da instalação
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. Iniciando debug...\n";

try {
    echo "2. Carregando crest.php...\n";
    require_once(__DIR__ . '/crest.php');
    echo "3. crest.php carregado com sucesso\n";
    
    echo "4. Carregando settings.php...\n";
    require_once(__DIR__ . '/settings.php');
    echo "5. settings.php carregado com sucesso\n";
    
    echo "6. Testando detecção Bitrix24...\n";
    $isBitrix24Install = isset($_REQUEST['code']) || isset($_REQUEST['domain']);
    echo "7. isBitrix24Install: " . ($isBitrix24Install ? 'true' : 'false') . "\n";
    
    if ($isBitrix24Install) {
        echo "8. Chamando CRest::installApp()...\n";
        $result = CRest::installApp();
        echo "9. installApp retornou: " . print_r($result, true) . "\n";
    } else {
        echo "8. Modo standalone\n";
        $result = [
            'rest_only' => false,
            'install' => false,
            'standalone' => true
        ];
        echo "9. Result standalone: " . print_r($result, true) . "\n";
    }
    
    echo "10. Testando funções auxiliares...\n";
    
    if (function_exists('getZenScribeConfig')) {
        echo "11. getZenScribeConfig existe\n";
        $config = getZenScribeConfig();
        echo "12. Config: " . print_r($config, true) . "\n";
    } else {
        echo "11. getZenScribeConfig NÃO existe\n";
    }
    
    if (function_exists('zenLog')) {
        echo "13. zenLog existe\n";
    } else {
        echo "13. zenLog NÃO existe\n";
    }
    
    echo "14. Debug concluído com sucesso!\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "ERRO FATAL: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
