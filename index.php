<?php

declare(strict_types=1);

header("content-type: text/plain;charset=utf8");

$raw = (string)($_POST ['code'] ?? '');
$pipes = [];
$proc = proc_open("sudo /opt/phpjail/php-7.3.6/bin/php /var/www/php-jailer/worker/jailworker.php", [
    0 => ["pipe", "rb"],
    1 => ["pipe", "wb"],
    2 => ["pipe", "wb"]
], $pipes);

fwrite($pipes [0], $raw);
fclose($pipes [0]);
while (($status = proc_get_status($proc)) ['running']) {
    usleep(100 * 1000); // 100 ms
    echo stream_get_contents($pipes [2]);
    echo stream_get_contents($pipes [1]);
}

echo stream_get_contents($pipes [2]);
fclose($pipes[2]);

echo stream_get_contents($pipes [1]);
fclose($pipes[1]);

proc_close($proc);
