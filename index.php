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

$current_category = isset($_GET['category']) ? $_GET['category'] : '';

// 分页设置
$per_page = 50;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

// 添加错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 获取总记录数
$count_sql = "SELECT COUNT(*) as total FROM mylink WHERE user_id = " . intval($_SESSION['user_id']);
if ($current_category) {
    $count_sql .= " AND type = '" . $db->escape($current_category) . "'";
}
$total_result = $db->query($count_sql);
if (!$total_result) {
    die("查询错误: " . mysql_error());
}
$total_row = mysql_fetch_assoc($total_result);
if (!$total_row) {
    die("获取总记录数失败");
}
$total_records = intval($total_row['total']);
$total_pages = max(1, ceil($total_records / $per_page));

// 确保页码在有效范围内
$page = min($page, $total_pages);

// 获取资源列表
$sql = "SELECT * FROM mylink WHERE user_id = " . intval($_SESSION['user_id']);
if ($current_category) {
    $sql .= " AND type = '" . $db->escape($current_category) . "'";
}
$sql .= " ORDER BY id DESC LIMIT $offset, $per_page";
$result = $db->query($sql);
if (!$result) {
    die("查询错误: " . mysql_error());
}

// 添加调试信息（可以在上线时删除）
if (isset($_GET['debug'])) {
    echo "<!--\n";
    echo "调试信息：\n";
    echo "总记录数：" . $total_records . "\n";
    echo "每页显示：" . $per_page . "\n";
    echo "当前页码：" . $page . "\n";
    echo "总页数：" . $total_pages . "\n";
    echo "SQL查询：" . $sql . "\n";
    echo "-->\n";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>会会资源导航</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <style>
        .nav-left .nav-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #333;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .nav-left .nav-logo i {
            font-size: 1.5rem;
            margin-right: 8px;
            color: #e74c3c;
        }

        .nav-right {
            display: flex;
            gap: 20px;
        }

        .nav-right a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #666;
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .nav-right a:hover {
            background: #f5f5f5;
            color: #e74c3c;
        }

        .nav-right a.active {
            background: #e74c3c;
            color: #fff;
        }

        .nav-right a i {
            margin-right: 6px;
            font-size: 1.1rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            gap: 10px;
        }
        .page-link {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            background: #fff;
            transition: all 0.3s ease;
        }
        .page-link:hover {
            background: #f5f5f5;
        }
        .page-link.active {
            background: #e74c3c;
            color: #fff;
            border-color: #e74c3c;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-left: 20px;
            color: #666;
        }
        
        .user-info i {
            margin-right: 5px;
            font-size: 1.2rem;
        }
        
        .logout-btn {
            margin-left: 15px;
            padding: 2px 5px;
            background-color: #efefef;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }

        .search-container {
            background: linear-gradient(-45deg, #ff6b6b, #e74c3c, #c0392b, #ff8e8e),
                        repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0px, rgba(255, 255, 255, 0.2) 1px, transparent 1px, transparent 10px);
            background-size: 400% 400%, 20px 20px;
            animation: gradient 15s ease infinite;
            padding: 40px 20px;
            border-radius: 10px;
            margin: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .search-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(-45deg, rgba(255, 255, 255, 0.15) 0px, rgba(255, 255, 255, 0.15) 1px, transparent 1px, transparent 10px);
            background-size: 20px 20px;
            pointer-events: none;
        }

        @keyframes gradient {
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

        .search-title {
            color: white;
            margin-bottom: 20px;
            font-size: 24px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .search-box {
            display: flex;
            justify-content: center;
            gap: 10px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-box button {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            background: white;
            color: #333;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .search-box button:hover {
            transform: scale(1.05);
        }

        .resource-card .icon {
            background: linear-gradient(-45deg, #ff6b6b, #ffd700, #e74c3c, #ffa500);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
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
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <header class="top-nav">
            <div class="nav-right">
                <a href="categories.php">
                    <i class="ri-folder-settings-line"></i>
                    <span>栏目管理</span>
                </a>
                <a href="import_bookmarks.php">
                    <i class="ri-bookmark-line"></i>
                    <span>导入书签</span>
                </a>
                <a href="export_bookmarks.php">
                    <i class="ri-download-cloud-line"></i>
                    <span>导出书签</span>
                </a>
                <div class="user-info">
                    <i class="ri-user-line"></i>
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="logout-btn">退出</a>
                </div>
            </div>
        </header>
        <div class="search-container">
            <h2 class="search-title">链接世界，发现无限可能！</h2>
            <div class="search-box">
                <input type="text" id="searchBox" placeholder="搜索资源...">
                <button onclick="search()"><i class="ri-search-line"></i></button>
            </div>
        </div>

        <h2 class="category-title">
            <?php echo $current_category ? htmlspecialchars($current_category) : '全部资源'; ?>
        </h2>

        <div class="resource-grid">
            <?php while ($row = mysql_fetch_assoc($result)): ?>
            <div class="resource-card">
                <div class="icon">
                    <i class="<?php echo $row['type'] === 'AI绘画' ? 'ri-palette-line' : 
                               ($row['type'] === 'AI聊天' ? 'ri-message-3-line' : 'ri-download-cloud-line'); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <span class="badge badge-primary"><?php echo htmlspecialchars($row['type']); ?></span>
                <?php if ($row['has']): ?>
                    <span class="badge badge-success">公开</span>
                <?php endif; ?>
                <div class="action-buttons">
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">
                        <i class="ri-edit-line"></i>
                    </a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="action-btn delete-btn" 
                       onclick="return confirm('确定要删除这个网址吗？')">
                        <i class="ri-delete-bin-line"></i>
                    </a>
                </div>
                <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank" style="text-decoration: none;">
                    <p style="margin-top: 10px; color: #666;">点击访问 <i class="ri-arrow-right-line"></i></p>
                </a>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1<?php echo $current_category ? '&category='.urlencode($current_category) : ''; ?>" class="page-link">
                    <i class="ri-arrow-left-double-line"></i> 首页
                </a>
                <a href="?page=<?php echo ($page-1); ?><?php echo $current_category ? '&category='.urlencode($current_category) : ''; ?>" class="page-link">
                    <i class="ri-arrow-left-s-line"></i> 上一页
                </a>
            <?php endif; ?>
            
            <?php
            // 显示页码，最多显示5个
            $start_page = max(1, min($page - 2, $total_pages - 4));
            $end_page = min($total_pages, $start_page + 4);
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <a href="?page=<?php echo $i; ?><?php echo $current_category ? '&category='.urlencode($current_category) : ''; ?>" 
                   class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page+1); ?><?php echo $current_category ? '&category='.urlencode($current_category) : ''; ?>" class="page-link">
                    下一页 <i class="ri-arrow-right-s-line"></i>
                </a>
                <a href="?page=<?php echo $total_pages; ?><?php echo $current_category ? '&category='.urlencode($current_category) : ''; ?>" class="page-link">
                    末页 <i class="ri-arrow-right-double-line"></i>
                </a>
            <?php endif; ?>
            
            <span style="margin-left: 10px; color: #666;">
                共 <?php echo $total_pages; ?> 页 / <?php echo $total_records; ?> 条记录
            </span>
        </div>
        <?php endif; ?>
    </div>

    <a href="add.php" class="add-btn">
        <i class="ri-add-line"></i>
    </a>

    <script>
        function search() {
            var query = document.getElementById('searchBox').value;
            if (query.trim()) {
                window.location.href = 'search.php?query=' + encodeURIComponent(query);
            }
        }
        
        document.getElementById('searchBox').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                search();
            }
        });
    </script>
</body>
</html>
<?php
$db->close();
?>
