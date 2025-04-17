<?php
// 数据库配置
define('DB_HOST', 'mysql.sql80.cdncenter.net');
define('DB_USER', 'sq_jhz1986');
define('DB_PASS', 'jhz1986');
define('DB_NAME', 'sq_jhz1986');
define('DB_CHARSET', 'utf8');

// 错误报告设置
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 设置默认字符集
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
?> 