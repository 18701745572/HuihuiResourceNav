<?php
if (!function_exists('password_hash')) {
    /**
     * 为旧版本PHP提供password_hash功能
     */
    function password_hash($password, $algo, $options = array()) {
        if (!function_exists('crypt')) {
            trigger_error("Crypt must be loaded for password_hash to function", E_USER_WARNING);
            return null;
        }
        
        // 生成一个随机的盐值
        $salt = '';
        for ($i = 0; $i < 22; $i++) {
            $salt .= substr('./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', mt_rand(0, 63), 1);
        }
        
        // 使用 SHA-512 算法
        $hash = crypt($password, '$6$' . $salt);
        
        if (strlen($hash) < 20) {
            return false;
        }
        
        return $hash;
    }
}

if (!function_exists('password_verify')) {
    /**
     * 为旧版本PHP提供password_verify功能
     */
    function password_verify($password, $hash) {
        if (strlen($hash) < 20) {
            return false;
        }
        
        $test = crypt($password, $hash);
        return ($test === $hash);
    }
}

// 定义PASSWORD_DEFAULT常量（如果不存在）
if (!defined('PASSWORD_DEFAULT')) {
    define('PASSWORD_DEFAULT', 1);
}

// 定义PASSWORD_BCRYPT常量（如果不存在）
if (!defined('PASSWORD_BCRYPT')) {
    define('PASSWORD_BCRYPT', 1);
}
?> 