<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    // 检查分类是否存在且属于当前用户
    $sql = "SELECT * FROM categories WHERE id = $id AND user_id = " . intval($_SESSION['user_id']);
    $result = $db->query($sql);
    if (mysql_fetch_assoc($result)) {
        // 删除分类
        $sql = "DELETE FROM categories WHERE id = $id AND user_id = " . intval($_SESSION['user_id']);
        $db->query($sql);
        
        // 将相关资源的分类设置为空
        $sql = "UPDATE mylink SET type = '' WHERE type = (SELECT name FROM categories WHERE id = $id) AND user_id = " . intval($_SESSION['user_id']);
        $db->query($sql);
    }
}

header('Location: categories.php');
exit; 