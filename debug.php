<?php
echo "Settings.json exists: " . (file_exists(__DIR__ . "/settings.json") ? "YES" : "NO") . "
";
echo "Timestamp: " . date("Y-m-d H:i:s") . "
";
?>
