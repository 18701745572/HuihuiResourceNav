<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

// 如果用户已经登录，直接跳转到首页
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (empty($username) || empty($password)) {
        $error = '用户名和密码不能为空！';
    } else {
        $db = new Database();
        
        $username = $db->escape($username);
        $hashed_password = md5($password);
        $sql = "SELECT id, username FROM users WHERE username = '$username' AND password = '$hashed_password'";
        $result = $db->query($sql);
        
        if ($result && mysql_num_rows($result) > 0) {
            $user = mysql_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = '用户名或密码错误！';
        }
        
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
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

        .login-container {
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

        .login-container::before {
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

        .login-container:hover::before {
            left: 100%;
        }

        .login-title {
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

        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #c0392b, #8e2c22);
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
            box-shadow: 0 2px 10px rgba(192, 57, 43, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(192, 57, 43, 0.5);
            background-position: right center;
        }

        .login-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 8px rgba(192, 57, 43, 0.4);
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

        .register-link {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .register-link a {
            color: #c0392b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #e74c3c;
            text-decoration: underline;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            25% { background-position: 50% 100%; }
            50% { background-position: 100% 50%; }
            75% { background-position: 50% 0%; }
            100% { background-position: 0% 50%; }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 16px;
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">用户登录</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">用户名：</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">登录</button>
        </form>
        <div class="register-link">
            还没有账号？<a href="register.php">立即注册</a>
        </div>
    </div>
</body>
</html> 