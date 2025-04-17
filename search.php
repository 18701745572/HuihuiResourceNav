<?php
require_once 'config.php';
require_once 'includes/Database.php';

$db = new Database();

// 获取搜索关键词
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$current_category = ''; // 用于sidebar.php

// 执行搜索
$sql = "SELECT * FROM mylink WHERE name LIKE '%" . $db->escape($query) . "%' OR url LIKE '%" . $db->escape($query) . "%' ORDER BY id DESC";
$result = $db->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>搜索结果 - 会会资源导航</title>
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

        .search-results {
            padding: 20px;
        }

        .search-header {
            margin-bottom: 20px;
        }

        .search-header h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .search-header p {
            color: #666;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 0 2px;
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
            </div>
        </header>

        <div class="search-container">
            <h2 class="search-title">搜索结果</h2>
            <div class="search-box">
                <input type="text" id="searchBox" placeholder="搜索资源..." value="<?php echo htmlspecialchars($query); ?>">
                <button onclick="search()"><i class="ri-search-line"></i></button>
            </div>
        </div>

        <div class="search-results">
            <div class="search-header">
                <h2>搜索："<?php echo htmlspecialchars($query); ?>"</h2>
            </div>

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
        </div>
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