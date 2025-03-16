<?php
$output = [];
$return_var = 0;

exec("/usr/bin/python3 /www/htdocs/v134538/verworfen.at/checker.py 2>&1", $output, $return_var);

if ($return_var !== 0) {
    echo "Cron job FAILED!";
    echo "<pre>" . print_r($output, true) . "</pre>";
    echo "Return code: " . $return_var;

    file_put_contents("/www/htdocs/v134538/verworfen.at/logs/cron_debug.log", 
        "FAILED - Return code: " . $return_var . "\nOutput:\n" . implode("\n", $output) . "\n\n", FILE_APPEND);
}
?>
