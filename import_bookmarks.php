<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 获取当前用户ID
$user_id = intval($_SESSION['user_id']);

// 开启错误显示
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new Database();
$debug_info = array();

function debug_log($step, $info) {
    global $debug_info;
    $debug_info[] = "<strong>步骤 {$step}:</strong> {$info}";
}

// 处理文件上传
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["bookmarks_file"])) {
    $file = $_FILES["bookmarks_file"];
    
    // 打印文件信息
    echo "<div class='debug-info'>";
    echo "<h3>文件信息：</h3>";
    echo "<pre>";
    echo "文件名: " . htmlspecialchars($file['name']) . "\n";
    echo "文件类型: " . htmlspecialchars($file['type']) . "\n";
    echo "文件大小: " . number_format($file['size'] / 1024, 2) . " KB\n";
    echo "临时文件: " . htmlspecialchars($file['tmp_name']) . "\n";
    echo "错误代码: " . $file['error'] . "\n";
    echo "</pre>";
    echo "</div>";
    
    debug_log(1, "文件上传信息：" . print_r($file, true));
    
    if ($file["error"] == 0) {
        $html = file_get_contents($file["tmp_name"]);
        debug_log(2, "成功读取文件，内容长度：" . strlen($html) . " 字节");
        
        // 创建DOM对象
        $dom = new DOMDocument();
        
        // 加载HTML内容，使用UTF-8编码
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        @$dom->loadHTML($html, LIBXML_NOERROR);
        debug_log(3, "HTML文件加载完成");
        
        // 获取所有书签链接
        $links = $dom->getElementsByTagName("a");
        $total_links = $links->length;
        debug_log(4, "找到书签链接数量：{$total_links}");
        
        // 打印书签节点的所有可用属性
        $first_link = $links->item(0);
        if ($first_link) {
            echo "<div class='debug-info'>";
            echo "<h3>书签节点可用属性（第一个书签示例）：</h3>";
            echo "<pre>";
            echo "节点名称: " . $first_link->nodeName . "\n";
            echo "节点类型: " . $first_link->nodeType . "\n";
            echo "文本内容: " . htmlspecialchars($first_link->nodeValue) . "\n";
            echo "\n属性列表:\n";
            foreach ($first_link->attributes as $attr) {
                echo "- " . $attr->nodeName . ": " . htmlspecialchars($attr->nodeValue) . "\n";
            }
            echo "</pre>";
            echo "</div>";
        }
        
        $success_count = 0;
        $error_count = 0;
        $duplicate_count = 0;
        $error_messages = array();
        $bookmarks_data = array(); // 存储要导入的书签数据
        
        // 首先收集所有书签数据
        foreach ($links as $index => $link) {
            $title = trim($link->nodeValue);
            $url = trim($link->getAttribute("href"));
            $add_date = $link->getAttribute("add_date");
            $icon = $link->getAttribute("icon");
            $last_modified = $link->getAttribute("last_modified");
            
            // 跳过空的书签
            if (empty($title) || empty($url)) {
                debug_log(5, "跳过空书签 #{$index}");
                continue;
            }
            
            $bookmarks_data[] = array(
                'title' => $title,
                'url' => $url,
                'add_date' => $add_date,
                'icon' => $icon,
                'last_modified' => $last_modified
            );
        }
        
        debug_log(6, "有效书签数量：" . count($bookmarks_data));
        
        // 打印书签数据
        echo "<div class='bookmarks-preview'>";
        echo "<h3>待导入的书签数据：</h3>";
        echo "<table class='bookmarks-table'>";
        echo "<tr>
                <th>序号</th>
                <th>标题</th>
                <th>URL</th>
                <th>添加时间</th>
                <th>图标</th>
                <th>最后修改</th>
              </tr>";
        foreach ($bookmarks_data as $index => $bookmark) {
            echo "<tr>";
            echo "<td>" . ($index + 1) . "</td>";
            echo "<td>" . htmlspecialchars($bookmark['title']) . "</td>";
            echo "<td>" . htmlspecialchars($bookmark['url']) . "</td>";
            echo "<td>" . ($bookmark['add_date'] ? date('Y-m-d H:i:s', $bookmark['add_date']) : '') . "</td>";
            echo "<td>" . htmlspecialchars($bookmark['icon']) . "</td>";
            echo "<td>" . ($bookmark['last_modified'] ? date('Y-m-d H:i:s', $bookmark['last_modified']) : '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        
        // 然后进行导入操作
        foreach ($bookmarks_data as $index => $bookmark) {
            $title = $bookmark['title'];
            $url = $bookmark['url'];
            
            debug_log(7, "处理书签 #" . ($index + 1) . ": {$title}");
            
            try {
                // 直接插入书签，不检查重复
                $title = $db->escape($title);
                $url = $db->escape($url);
                $sql = "INSERT INTO mylink (name, url, type, has, user_id) VALUES ('{$title}', '{$url}', '未分类', 0, {$user_id})";
                debug_log(8, "执行插入SQL: " . $sql);
                
                if ($db->query($sql)) {
                    $success_count++;
                    debug_log(9, "成功插入书签: {$title}");
                } else {
                    throw new Exception(mysql_error());
                }
            } catch (Exception $e) {
                $error_count++;
                $error_message = "处理书签 '{$title}' 时出错: " . $e->getMessage();
                $error_messages[] = $error_message;
                debug_log(10, "错误: " . $error_message);
            }
        }
        
        $message = "导入完成！成功导入 {$success_count} 个书签，失败 {$error_count} 个。";
        debug_log(11, $message);
        
        if ($error_count > 0) {
            $message .= "<br><br>错误详情：<br>" . implode("<br>", $error_messages);
        }
    } else {
        $message = "文件上传失败，错误代码：" . $file["error"];
        debug_log('ERROR', $message);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>导入Chrome书签</title>
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
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .upload-form {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 4px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .back-link {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 1000;
        }
        .back-link:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            color: #fff;
        }
        .back-link i {
            font-size: 1.2em;
        }
        .error-details {
            margin-top: 10px;
            padding: 10px;
            background-color: #fff3f3;
            border-left: 4px solid #dc3545;
        }
        .bookmarks-preview {
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .bookmarks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .bookmarks-table th,
        .bookmarks-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .bookmarks-table th {
            background-color: #f1f1f1;
        }
        .bookmarks-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .bookmarks-table tr:hover {
            background-color: #f5f5f5;
        }
        .debug-info {
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #17a2b8;
        }
        .debug-info pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        #file-preview {
            display: none;
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #28a745;
        }
        .preview-content {
            max-height: 400px;
            overflow-y: auto;
        }
        .user-info {
            background-color: #e3f2fd;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #1976d2;
        }
        .user-info p {
            margin: 0;
            color: #1565c0;
            font-size: 14px;
        }
        .file-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="ri-bookmark-line"></i> 导入Chrome书签</h1>
        <div class="user-info">
            <p>当前用户ID：<?php echo htmlspecialchars($user_id); ?></p>
        </div>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, '成功') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="upload-form">
            <form method="post" enctype="multipart/form-data" id="upload-form">
                <p>请选择Chrome导出的书签文件（bookmarks_*.html）：</p>
                <input type="file" name="bookmarks_file" accept=".html" required id="file-input">
                <br><br>
                <div id="file-preview">
                    <h3>文件预览</h3>
                    <div class="file-info"></div>
                    <div class="preview-content"></div>
                </div>
                <input type="submit" value="开始导入">
            </form>
        </div>
        
        <p>使用说明：</p>
        <ol>
            <li>在Chrome浏览器中打开书签管理器（按 Ctrl+Shift+O）</li>
            <li>点击右上角的"更多"按钮，选择"导出书签"</li>
            <li>选择保存位置，文件名类似 bookmarks_2025_3_5.html</li>
            <li>在此页面上传该文件即可导入书签</li>
            <li>导入的书签将默认分类为"未分类"，您可以在首页编辑分类</li>
        </ol>
        
        <a href="index.php" class="back-link">
            <i class="ri-arrow-left-line"></i> 返回首页
        </a>
    </div>

    <script>
    document.getElementById('file-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const preview = document.getElementById('file-preview');
        const fileInfo = preview.querySelector('.file-info');
        const previewContent = preview.querySelector('.preview-content');

        // 显示文件基本信息
        fileInfo.innerHTML = `
            <div style="background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4 style="margin-top: 0;">📁 文件信息</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px;"><strong>文件名：</strong></td>
                        <td style="padding: 5px;">${file.name}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;"><strong>大小：</strong></td>
                        <td style="padding: 5px;">${(file.size / 1024).toFixed(2)} KB</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;"><strong>类型：</strong></td>
                        <td style="padding: 5px;">${file.type || 'text/html'}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px;"><strong>最后修改：</strong></td>
                        <td style="padding: 5px;">${new Date(file.lastModified).toLocaleString()}</td>
                    </tr>
                </table>
            </div>
        `;

        // 读取文件内容
        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            
            // 创建临时 DOM 解析 HTML
            const parser = new DOMParser();
            const doc = parser.parseFromString(content, 'text/html');
            const links = doc.getElementsByTagName('a');
            
            if (links.length > 0) {
                // 分析第一个书签的所有属性
                const firstLink = links[0];
                const attributes = Array.from(firstLink.attributes);
                
                let bookmarkFields = `
                    <div style="background: #fff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #ddd;">
                        <h4 style="margin-top: 0;">📑 书签字段分析</h4>
                        <p><strong>总书签数：</strong>${links.length}</p>
                        <p><strong>可用字段：</strong></p>
                        <ul style="list-style-type: none; padding-left: 0;">
                            <li>✓ 标题 (title/text)</li>
                            <li>✓ 网址 (href/url)</li>
                            ${attributes.map(attr => `<li>✓ ${attr.name} (${attr.value.slice(0, 50)}${attr.value.length > 50 ? '...' : ''})</li>`).join('')}
                        </ul>
                    </div>
                `;

                // 显示前5个书签的预览
                let bookmarksPreview = `
                    <div style="background: #fff; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                        <h4 style="margin-top: 0;">👀 书签预览（前5个）</h4>
                        <table class="bookmarks-table" style="width: 100%; border-collapse: collapse;">
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">序号</th>
                                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">标题</th>
                                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">URL</th>
                                <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">添加时间</th>
                            </tr>
                            ${Array.from(links).slice(0, 5).map((link, index) => `
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${index + 1}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${link.textContent}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${link.href}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">${link.getAttribute('add_date') ? new Date(parseInt(link.getAttribute('add_date')) * 1000).toLocaleString() : '无'}</td>
                                </tr>
                            `).join('')}
                        </table>
                    </div>
                `;

                previewContent.innerHTML = bookmarkFields + bookmarksPreview;
            } else {
                previewContent.innerHTML = `
                    <div style="background: #fff3cd; padding: 15px; border-radius: 5px; color: #856404; border: 1px solid #ffeeba;">
                        <h4 style="margin-top: 0;">⚠️ 警告</h4>
                        <p>未在文件中找到任何书签数据。请确保选择了正确的Chrome书签导出文件。</p>
                    </div>
                `;
            }
        };

        reader.readAsText(file);
        preview.style.display = 'block';
    });

    // 添加表单提交处理
    document.getElementById('upload-form').addEventListener('submit', function(e) {
        e.preventDefault(); // 阻止表单默认提交
        
        const file = document.getElementById('file-input').files[0];
        if (!file) {
            alert('请先选择书签文件！');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            const parser = new DOMParser();
            const doc = parser.parseFromString(content, 'text/html');
            const links = doc.getElementsByTagName('a');
            
            // 准备要导入的数据
            const bookmarks = Array.from(links).map(link => ({
                title: link.textContent.trim(),
                url: link.href,
                add_date: link.getAttribute('add_date')
            })).filter(bookmark => bookmark.title && bookmark.url);

            // 发送数据到服务器
            fetch('import_bookmarks_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ bookmarks: bookmarks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`导入成功！\n成功：${data.success_count} 个\n失败：${data.error_count} 个`);
                    window.location.href = 'index.php'; // 导入成功后跳转到首页
                } else {
                    alert('导入失败：' + data.message);
                }
            })
            .catch(error => {
                alert('导入出错：' + error.message);
            });
        };

        reader.readAsText(file);
    });
    </script>
</body>
</html>
<?php
$db->close();
?> 