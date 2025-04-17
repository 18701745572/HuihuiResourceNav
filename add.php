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
$sql = "SELECT * FROM categories WHERE user_id = " . intval($_SESSION['user_id']) . " ORDER BY name ASC";
$result = $db->query($sql);
$categories = array();
while ($row = mysql_fetch_assoc($result)) {
    $categories[] = $row;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $url = isset($_POST['url']) ? trim($_POST['url']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $has = isset($_POST['has']) ? 1 : 0;

    if ($name && $url && $type) {
        $sql = "INSERT INTO mylink (name, url, type, has, user_id) VALUES (
            '" . $db->escape($name) . "',
            '" . $db->escape($url) . "',
            '" . $db->escape($type) . "',
            " . $has . ",
            " . intval($_SESSION['user_id']) . "
        )";
        
        if ($db->query($sql)) {
            header('Location: index.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>添加新资源 - 会会资源导航</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #e74c3c;
            outline: none;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .submit-btn {
            background: #e74c3c;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #c0392b;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }

        .back-link:hover {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">添加新资源</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">资源名称</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="url">资源链接</label>
                <input type="url" id="url" name="url" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="type">资源分类</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">请选择分类</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['name']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="has" name="has" value="1">
                    <label for="has">公开</label>
                </div>
            </div>

            <button type="submit" class="submit-btn">添加资源</button>
        </form>
        <a href="index.php" class="back-link">
            <i class="ri-arrow-left-line"></i> 返回首页
        </a>
    </div>
</body>
</html>
<?php
$db->close();
?> 