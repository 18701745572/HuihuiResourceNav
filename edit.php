<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

$db = new Database();

// 获取当前用户ID
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// 获取当前用户的分类
$sql = "SELECT * FROM categories WHERE user_id = " . $user_id . " ORDER BY name ASC";
$result = $db->query($sql);
$categories = array();
while ($row = mysql_fetch_assoc($result)) {
    $categories[] = $row;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 获取当前记录
$sql = "SELECT * FROM mylink WHERE id = " . $id;
$result = $db->query($sql);
$row = mysql_fetch_assoc($result);

if (!$row) {
    header('Location: index.php');
    exit;
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $url = isset($_POST['url']) ? trim($_POST['url']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $has = isset($_POST['has']) ? 1 : 0;
    
    if (!empty($name) && !empty($url) && !empty($type)) {
        $sql = "UPDATE mylink SET 
            name = '" . $db->escape($name) . "',
            url = '" . $db->escape($url) . "',
            type = '" . $db->escape($type) . "',
            has = " . $has . "
            WHERE id = " . $id;
        
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
    <title>编辑网址 - 会会资源导航</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff4d4d;
            --primary-gradient: linear-gradient(135deg, #ff4d4d 0%, #ff1a1a 100%);
            --secondary-color: #2ecc71;
            --text-color: #2c3e50;
            --bg-color: #f5f6fa;
            --card-bg: #ffffff;
            --border-color: #e1e8ed;
            --shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            --container-width: 800px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(-45deg, #ffebee, #ffcdd2, #ff8a80, #ff5252);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .main-content {
            width: 100%;
            max-width: var(--container-width);
            margin: 40px auto;
            padding: 0 20px;
            position: relative;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px 30px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .page-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .back-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: 12px;
            background: rgba(255, 77, 77, 0.1);
            font-size: 15px;
        }

        .back-link:hover {
            background: rgba(255, 77, 77, 0.2);
            transform: translateX(-5px);
        }

        .back-link i {
            margin-right: 8px;
            font-size: 18px;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .form-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text-color);
            font-size: 15px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            color: var(--text-color);
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.1);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
            border-radius: 4px;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-size: 15px;
            color: var(--text-color);
        }

        .form-footer {
            margin-top: 40px;
            text-align: right;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            min-width: 120px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 77, 77, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 77, 77, 0.4);
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 0;
            }

            .main-content {
                padding: 0 15px;
            }

            .form-card {
                padding: 25px;
                border-radius: 16px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
                padding: 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .btn {
                width: 100%;
                padding: 12px 24px;
            }
        }
    </style>
</head>
<body>


    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">编辑网址</h1>
            <a href="index.php" class="back-link">
                <i class="ri-arrow-left-line"></i>
                返回首页
            </a>
        </div>

        <div class="form-card">
            <form method="post" action="">
                <div class="form-group">
                    <label for="name">网站名称</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="url">网站地址</label>
                    <input type="url" id="url" name="url" class="form-control" 
                           value="<?php echo htmlspecialchars($row['url']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="type">分类</label>
                    <select id="type" name="type" class="form-select" required>
                        <option value="">请选择分类</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['name']); ?>" 
                                    <?php echo $row['type'] === $category['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="has" name="has" value="1" 
                               <?php echo $row['has'] ? 'checked' : ''; ?>>
                        <label for="has">公开</label>
                    </div>
                </div>
                
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">保存修改</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php
$db->close();
?> 