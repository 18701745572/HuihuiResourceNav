<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        'success' => false,
        'message' => '请先登录'
    ));
    exit;
}

// 获取当前用户ID
$user_id = intval($_SESSION['user_id']);

// 设置响应头
header('Content-Type: application/json');

try {
    // 获取POST数据
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['bookmarks']) || !is_array($data['bookmarks'])) {
        throw new Exception('无效的数据格式');
    }

    $db = new Database();
    $success_count = 0;
    $error_count = 0;
    $duplicate_count = 0;
    $errors = array();

    foreach ($data['bookmarks'] as $bookmark) {
        $title = trim($bookmark['title']);
        $url = trim($bookmark['url']);
        
        // 跳过空的书签
        if (empty($title) || empty($url)) {
            continue;
        }

        // 检查URL是否已存在于当前用户的书签中
        $check_sql = "SELECT COUNT(*) as count FROM mylink WHERE url = '" . $db->escape($url) . "' AND user_id = " . $user_id;
        $check_result = $db->query($check_sql);
        $row = mysql_fetch_assoc($check_result);
        
        if ($row['count'] > 0) {
            $duplicate_count++;
            continue;
        }

        try {
            // 插入数据
            $title = $db->escape($title);
            $url = $db->escape($url);
            $sql = "INSERT INTO mylink (name, url, type, has, user_id) VALUES ('{$title}', '{$url}', '未分类', 0, {$user_id})";
            
            if ($db->query($sql)) {
                $success_count++;
            } else {
                throw new Exception(mysql_error());
            }
        } catch (Exception $e) {
            $error_count++;
            $errors[] = "处理书签 '{$title}' 时出错: " . $e->getMessage();
        }
    }

    // 返回结果
    echo json_encode(array(
        'success' => true,
        'success_count' => $success_count,
        'error_count' => $error_count,
        'duplicate_count' => $duplicate_count,
        'errors' => $errors
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}

// 关闭数据库连接
if (isset($db)) {
    $db->close();
}
?> 