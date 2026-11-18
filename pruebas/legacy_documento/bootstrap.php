<?php
declare(strict_types=1);
if (!getenv('QA_DOCUMENTO_NETNS') || file_exists('/etc/mysql') || count(scandir('/home')) !== 2) {
    throw new RuntimeException('Aislamiento requerido');
}
require '/var/www/html/documento/config/database.php';
require '/var/www/html/documento/config/generales.php';
require '/var/www/html/documento/vendor/autoload.php';
$_SESSION = [];
$_POST = [];
$_FILES = [];
