<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

// 设置字符集
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_regex_encoding('UTF-8');

// 如果用户已经登录，直接跳转到首页
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $error = '所有字段都必须填写！';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址！';
    } elseif ($password !== $confirm_password) {
        $error = '两次输入的密码不一致！';
    } else {
        $db = new Database();
        
        // 检查用户名是否已存在
        $username = $db->escape($username);
        $email = $db->escape($email);
        $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $result = $db->query($check_sql);
        
        if ($result && mysql_num_rows($result) > 0) {
            $error = '用户名或邮箱已存在！';
        } else {
            $hashed_password = md5($password);
            $current_time = date('Y-m-d H:i:s');
            $sql = "INSERT INTO users (username, email, password, created_at) VALUES ('$username', '$email', '$hashed_password', '$current_time')";
            
            if ($db->query($sql)) {
                $success = '注册成功！正在跳转到登录页面...';
                header('Refresh: 2; URL=login.php');
            } else {
                $error = '注册失败，请稍后重试！';
            }
        }
        
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>用户注册</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(45deg, #1a1a1a, #2d2d2d, #3d1515, #2d1212);
            background-size: 300% 300%;
            animation: gradientBG 15s ease infinite;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(123, 28, 28, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(123, 28, 28, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .register-container {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 4px 24px -1px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            margin: 20px;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            transition: 0.5s;
        }

        .register-container:hover::before {
            left: 100%;
        }

        .register-title {
            text-align: center;
            margin-bottom: 40px;
            color: #fff;
            font-weight: 600;
            font-size: 28px;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: rgba(197, 164, 126, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(197, 164, 126, 0.1);
        }

        .register-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7b1c1c, #5c1515);
            background-size: 200% 200%;
            animation: buttonGradient 3s ease infinite;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(123, 28, 28, 0.3);
        }

        .register-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(123, 28, 28, 0.5);
            background-position: right center;
        }

        .register-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 8px rgba(123, 28, 28, 0.4);
        }

        @keyframes buttonGradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .error-message {
            color: #ff6b6b;
            margin-bottom: 24px;
            text-align: center;
            padding: 10px;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 6px;
            font-size: 14px;
        }

        .success-message {
            color: #69db7c;
            margin-bottom: 24px;
            text-align: center;
            padding: 10px;
            background: rgba(105, 219, 124, 0.1);
            border-radius: 6px;
            font-size: 14px;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .login-link a {
            color: #7b1c1c;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #a82828;
            text-decoration: underline;
        }

        .password-requirements {
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            line-height: 1.5;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            25% { background-position: 50% 100%; }
            50% { background-position: 100% 50%; }
            75% { background-position: 50% 0%; }
            100% { background-position: 0% 50%; }
        }

        @media (max-width: 480px) {
            .register-container {
                margin: 16px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="register-title">用户注册</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">用户名：</label>
                <input type="text" id="username" name="username" required>
                <div class="password-requirements">用户名长度至少3个字符</div>
            </div>
            <div class="form-group">
                <label for="email">邮箱：</label>
                <input type="email" id="email" name="email" required>
                <div class="password-requirements">请输入有效的邮箱地址</div>
            </div>
            <div class="form-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" required>
                <div class="password-requirements">密码长度至少6个字符，建议包含字母和数字</div>
            </div>
            <div class="form-group">
                <label for="confirm_password">确认密码：</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="register-btn">注册</button>
        </form>
        <div class="login-link">
            已有账号？<a href="login.php">立即登录</a>
        </div>
    </div>
</body>
</html> 