<?php
session_start();

?>

<!DOCTYPE html>
<html>
<head>
    <title>导出书签</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .bookmarks-list {
            margin: 25px 0;
        }
        .bookmark-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .bookmark-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        .bookmark-checkbox {
            margin-right: 15px;
            cursor: pointer;
        }
        .bookmark-info {
            flex-grow: 1;
        }
        .bookmark-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        .bookmark-url {
            color: #666;
            font-size: 0.9em;
            word-break: break-all;
        }
        .export-form {
            margin: 25px 0;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }
        .button {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(255, 71, 87, 0.2);
        }
        .button:hover {
            background: linear-gradient(135deg, #ff4757 0%, #ff6b6b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 71, 87, 0.3);
        }
        .back-link {
            position: fixed;
            top: 30px;
            right: 30px;
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: white;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .back-link:hover {
            color: #ff4757;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .back-link i {
            margin-right: 5px;
        }
        .filter-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .filter-input {
            padding: 10px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .select-buttons {
            margin: 15px 0;
            display: flex;
            gap: 10px;
        }
        .select-buttons button {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .select-buttons button:hover {
            background: linear-gradient(135deg, #ff4757 0%, #ff6b6b 100%);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="ri-bookmark-line"></i> 导出书签</h1>
        
        <div class="filter-section">
            <input type="text" id="searchInput" class="filter-input" placeholder="搜索书签...">
            <select id="typeFilter" class="filter-input">
                <option value="">所有分类</option>
                <?php
                require_once 'config.php';
                require_once 'includes/Database.php';
                
                // 检查用户是否已登录
                if (!isset($_SESSION['user_id'])) {
                    header('Location: login.php');
                    exit();
                }

                $db = new Database();
                $sql = "SELECT DISTINCT type FROM mylink WHERE user_id = " . intval($_SESSION['user_id']) . " AND type != '' ORDER BY type";
                $result = $db->query($sql);
                
                if ($result) {
                    while ($row = mysql_fetch_assoc($result)) {
                        echo "<option value='" . htmlspecialchars($row['type']) . "'>" . 
                             htmlspecialchars($row['type']) . "</option>";
                    }
                }
                $db->close();
                ?>
            </select>
        </div>

        <div class="select-buttons">
            <button onclick="selectAll()" class="button">全选</button>
            <button onclick="deselectAll()" class="button">取消全选</button>
        </div>

        <form method="post" action="export_bookmarks_process.php" id="exportForm">
            <div class="bookmarks-list">
                <?php
                require_once 'config.php';
                require_once 'includes/Database.php';

                // 检查用户是否已登录
                if (!isset($_SESSION['user_id'])) {
                    header('Location: login.php');
                    exit();
                }

                $db = new Database();
                $sql = "SELECT * FROM mylink WHERE user_id = " . intval($_SESSION['user_id']) . " ORDER BY type, name";
                $result = $db->query($sql);
                
                while ($row = mysql_fetch_assoc($result)) {
                    echo "<div class='bookmark-item' data-type='" . htmlspecialchars($row['type']) . "'>";
                    echo "<input type='checkbox' name='bookmarks[]' value='" . $row['id'] . 
                         "' class='bookmark-checkbox'>";
                    echo "<div class='bookmark-info'>";
                    echo "<div class='bookmark-title'>" . htmlspecialchars($row['name']) . "</div>";
                    echo "<div class='bookmark-url'>" . htmlspecialchars($row['url']) . "</div>";
                    echo "<div class='bookmark-type'>分类：" . htmlspecialchars($row['type']) . "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                
                $db->close();
                ?>
            </div>
            <div class="export-form">
                <button type="submit" class="button">导出选中的书签</button>
            </div>
        </form>

        <a href="index.php" class="back-link">
            <i class="ri-arrow-left-line"></i> 返回首页
        </a>
    </div>

    <script>
    // 搜索和筛选功能
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const bookmarkItems = document.querySelectorAll('.bookmark-item');

    function filterBookmarks() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;

        bookmarkItems.forEach(item => {
            const title = item.querySelector('.bookmark-title').textContent.toLowerCase();
            const url = item.querySelector('.bookmark-url').textContent.toLowerCase();
            const type = item.dataset.type;
            
            const matchesSearch = title.includes(searchTerm) || url.includes(searchTerm);
            const matchesType = !selectedType || type === selectedType;

            item.style.display = matchesSearch && matchesType ? 'flex' : 'none';
        });
    }

    searchInput.addEventListener('input', filterBookmarks);
    typeFilter.addEventListener('change', filterBookmarks);

    // 全选/取消全选功能
    function selectAll() {
        document.querySelectorAll('.bookmark-checkbox').forEach(checkbox => {
            const item = checkbox.closest('.bookmark-item');
            if (item.style.display !== 'none') {
                checkbox.checked = true;
            }
        });
    }

    function deselectAll() {
        document.querySelectorAll('.bookmark-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    // 表单提交验证
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('input[name="bookmarks[]"]:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('请至少选择一个书签进行导出！');
        }
    });
    </script>
</body>
</html> 