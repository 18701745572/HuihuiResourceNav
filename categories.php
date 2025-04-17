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
$result = $db->query($sql);
if (!$result) {
    die("查询错误: " . mysql_error());
}

$categories = array();
while ($row = mysql_fetch_assoc($result)) {
    $categories[] = $row;
}

// 添加调试信息
error_log("SQL查询: " . $sql);
error_log("查询结果数量: " . count($categories));
if (empty($categories)) {
    error_log("警告：没有找到任何分类数据");
} else {
    error_log("找到的分类数据: " . print_r($categories, true));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>分类管理 - 会会资源导航</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">分类管理</h1>
            <div class="header-actions">
                <a href="icon_selector.php" class="btn btn-primary" style="margin-right: 10px;">
                    <i class="ri-palette-line"></i> 选择图标
                </a>
                <a href="category_add.php" class="btn btn-primary">
                    <i class="ri-add-line"></i> 添加分类
                </a>
            </div>
        </div>

        <div class="form-card">
            <div class="category-list">
                <?php foreach ($categories as $row): ?>
                <div class="category-item">
                    <div class="category-info">
                        <i class="<?php echo htmlspecialchars($row['icon']); ?>"></i>
                        <span><?php echo htmlspecialchars($row['name']); ?></span>
                    </div>
                    <div class="category-actions">
                        <a href="category_edit.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">
                            <i class="ri-edit-line"></i>
                        </a>
                        <a href="category_delete.php?id=<?php echo $row['id']; ?>" 
                           class="action-btn delete-btn"
                           onclick="return confirm('确定要删除这个分类吗？删除后相关资源将变为未分类状态。')">
                            <i class="ri-delete-bin-line"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$db->close();
?> 