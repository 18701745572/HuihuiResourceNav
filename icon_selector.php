<?php
require_once 'config.php';
require_once 'includes/Database.php';

$db = new Database();

// 获取所有分类
$sql = "SELECT * FROM categories ORDER BY id DESC";
$categories_result = $db->query($sql);
$categories = array();
while ($row = mysql_fetch_assoc($categories_result)) {
    $categories[] = $row['name'];
}

// 定义常用图标
$icons = array(
    '基础图标' => array(
        'ri-home-4-line' => '首页',
        'ri-compass-3-line' => '指南针',
        'ri-settings-3-line' => '设置',
        'ri-menu-line' => '菜单',
        'ri-search-line' => '搜索',
        'ri-add-line' => '添加',
        'ri-edit-line' => '编辑',
        'ri-delete-bin-line' => '删除',
        'ri-arrow-left-line' => '左箭头',
        'ri-arrow-right-line' => '右箭头',
        'ri-arrow-up-line' => '上箭头',
        'ri-arrow-down-line' => '下箭头',
    ),
    '分类图标' => array(
        'ri-folder-line' => '文件夹',
        'ri-folder-settings-line' => '文件夹设置',
        'ri-folder-add-line' => '添加文件夹',
        'ri-folder-upload-line' => '上传文件夹',
        'ri-folder-download-line' => '下载文件夹',
    ),
    '资源图标' => array(
        'ri-palette-line' => '调色板',
        'ri-message-3-line' => '消息',
        'ri-download-cloud-line' => '云下载',
        'ri-link' => '链接',
        'ri-global-line' => '全球',
        'ri-earth-line' => '地球',
        'ri-window-line' => '窗口',
        'ri-apps-2-line' => '应用',
    ),
    '工具图标' => array(
        'ri-tools-line' => '工具',
        'ri-code-line' => '代码',
        'ri-terminal-box-line' => '终端',
        'ri-bug-line' => '调试',
        'ri-file-code-line' => '代码文件',
        'ri-file-text-line' => '文本文件',
        'ri-file-list-line' => '文件列表',
    ),
    '媒体图标' => array(
        'ri-image-line' => '图片',
        'ri-video-line' => '视频',
        'ri-music-line' => '音乐',
        'ri-film-line' => '电影',
        'ri-camera-line' => '相机',
        'ri-gallery-line' => '图库',
    ),
    '社交图标' => array(
        'ri-user-line' => '用户',
        'ri-team-line' => '团队',
        'ri-share-line' => '分享',
        'ri-chat-1-line' => '聊天',
        'ri-message-2-line' => '消息',
        'ri-notification-line' => '通知',
    ),
    '其他图标' => array(
        'ri-heart-line' => '心形',
        'ri-star-line' => '星星',
        'ri-bookmark-line' => '书签',
        'ri-tag-line' => '标签',
        'ri-calendar-line' => '日历',
        'ri-time-line' => '时间',
        'ri-map-pin-line' => '地图标记',
        'ri-mail-line' => '邮件',
        'ri-phone-line' => '电话',
    ),
);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>图标选择器 - 会会资源导航</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <style>
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .icon-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .icon-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .icon-item i {
            font-size: 24px;
            margin-right: 15px;
            color: var(--primary-color);
        }
        .icon-info {
            flex: 1;
        }
        .icon-name {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .icon-class {
            font-family: monospace;
            font-size: 12px;
            color: #999;
        }
        .copy-btn {
            padding: 6px 12px;
            border-radius: 4px;
            background: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .copy-btn:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }
        .copy-btn i {
            font-size: 14px;
            margin: 0;
        }
        .copy-btn.copied {
            background: var(--secondary-color);
        }
        .category-title {
            font-size: 20px;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">图标选择器</h1>
            <a href="categories.php" class="btn btn-primary">
                <i class="ri-arrow-left-line"></i> 返回分类管理
            </a>
        </div>

        <div class="form-card">
            <?php foreach ($icons as $category => $category_icons): ?>
            <h2 class="category-title"><?php echo $category; ?></h2>
            <div class="icon-grid">
                <?php foreach ($category_icons as $class => $name): ?>
                <div class="icon-item">
                    <i class="<?php echo $class; ?>"></i>
                    <div class="icon-info">
                        <div class="icon-name"><?php echo $name; ?></div>
                        <div class="icon-class"><?php echo $class; ?></div>
                    </div>
                    <button class="copy-btn" onclick="copyIconClass('<?php echo $class; ?>', this)">
                        <i class="ri-file-copy-line"></i>
                        <span>复制</span>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function copyIconClass(className, button) {
            // 创建临时输入框
            const tempInput = document.createElement('input');
            tempInput.value = className;
            document.body.appendChild(tempInput);
            
            // 选择文本
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // 兼容移动设备
            
            try {
                // 尝试使用新的 Clipboard API
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(className).then(function() {
                        updateButtonState(button);
                    }).catch(function(err) {
                        console.error('复制失败:', err);
                        fallbackCopy();
                    });
                } else {
                    fallbackCopy();
                }
            } catch (err) {
                console.error('复制失败:', err);
                fallbackCopy();
            }
            
            function fallbackCopy() {
                try {
                    // 使用传统的 document.execCommand 方法
                    document.execCommand('copy');
                    updateButtonState(button);
                } catch (err) {
                    console.error('复制失败:', err);
                    alert('复制失败，请手动复制');
                }
            }
            
            function updateButtonState(btn) {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ri-check-line"></i><span>已复制</span>';
                btn.classList.add('copied');
                
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('copied');
                }, 2000);
            }
            
            // 移除临时输入框
            document.body.removeChild(tempInput);
        }
    </script>
</body>
</html>
<?php
$db->close();
?> 