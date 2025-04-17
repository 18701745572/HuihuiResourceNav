<?php
class Database {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    
    public function __construct() {
        require_once dirname(__FILE__) . '/../config.php';
        
        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
        $this->charset = DB_CHARSET;
        
        try {
            $this->connect();
        } catch (Exception $e) {
            error_log("数据库连接错误: " . $e->getMessage());
            throw $e;
        }
    }
    
    private function connect() {
        // 记录连接尝试
        error_log("尝试连接到数据库: {$this->host}");
        
        $this->connection = @mysql_connect($this->host, $this->username, $this->password);
        
        if (!$this->connection) {
            throw new Exception('数据库连接失败: ' . mysql_error());
        }
        
        error_log("数据库连接成功");
        
        if (!mysql_select_db($this->database, $this->connection)) {
            throw new Exception('无法选择数据库: ' . mysql_error());
        }
        
        error_log("成功选择数据库: {$this->database}");
        
        // 设置数据库连接字符集
        if (!mysql_set_charset($this->charset, $this->connection)) {
            error_log("警告：设置字符集失败，尝试使用查询设置");
            // 如果 mysql_set_charset 失败，尝试使用查询设置
            $this->query("SET NAMES '{$this->charset}'");
            $this->query("SET CHARACTER SET {$this->charset}");
            $this->query("SET character_set_connection={$this->charset}");
            $this->query("SET character_set_results={$this->charset}");
            $this->query("SET character_set_client={$this->charset}");
        }
        
        error_log("数据库字符集设置完成");
    }
    
    public function query($sql) {
        error_log("执行SQL查询: " . $sql);
        
        $result = mysql_query($sql, $this->connection);
        if (!$result) {
            $error = mysql_error();
            error_log("SQL查询错误: " . $error);
            throw new Exception('数据库查询错误: ' . $error);
        }
        
        error_log("SQL查询执行成功");
        return $result;
    }
    
    public function escape($string) {
        if (!is_string($string)) {
            error_log("警告：escape方法接收到非字符串值：" . gettype($string));
            $string = strval($string);
        }
        return mysql_real_escape_string($string, $this->connection);
    }
    
    public function close() {
        if ($this->connection) {
            error_log("关闭数据库连接");
            mysql_close($this->connection);
        }
    }
} 