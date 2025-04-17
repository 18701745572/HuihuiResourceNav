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

// 获取所有分类
$sql = "SELECT * FROM categories WHERE user_id = " . intval($_SESSION['user_id']) . " ORDER BY id DESC";
$categories_result = $db->query($sql);
$categories = array();
while ($row = mysql_fetch_assoc($categories_result)) {
    $categories[] = $row['name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    
    if (!empty($name) && !empty($icon)) {
        $sql = "INSERT INTO categories (name, icon, user_id) VALUES (
            '" . $db->escape($name) . "', 
            '" . $db->escape($icon) . "',
            " . intval($_SESSION['user_id']) . "
        )";
        if ($db->query($sql)) {
            header('Location: categories.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>添加分类 - 会会资源导航</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">添加分类</h1>
            <a href="categories.php" class="btn btn-primary">
                <i class="ri-arrow-left-line"></i> 返回列表
            </a>
        </div>

        <div class="form-card">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">分类名称</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="icon">图标类名</label>
                    <div class="input-group">
                        <input type="text" id="icon" name="icon" class="form-control" 
                               placeholder="例如：ri-palette-line" required>
                        <a href="icon_selector.php" class="btn btn-primary" style="margin-left: 10px;">
                            <i class="ri-palette-line"></i> 选择图标
                        </a>
                    </div>
                    <small class="form-text">请使用 Remix Icon 的图标类名</small>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line"></i> 保存
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
$db->close();
?> 