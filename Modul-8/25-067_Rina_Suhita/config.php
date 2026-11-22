<?php
$root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$dir  = str_replace('\\', '/', __DIR__);

define('BASE_URL', rtrim(str_ireplace($root, '', $dir), '/'));  