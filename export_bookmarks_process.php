<?php
session_start();
require_once 'config.php';
require_once 'includes/Database.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 检查是否有选中的书签
if (!isset($_POST['bookmarks']) || empty($_POST['bookmarks'])) {
    header('Location: export_bookmarks.php');
    exit;
}

$db = new Database();

// 获取选中的书签数据
$bookmark_ids = array_map('intval', $_POST['bookmarks']);
$ids_string = implode(',', $bookmark_ids);
$sql = "SELECT * FROM mylink WHERE id IN ($ids_string) AND user_id = " . intval($_SESSION['user_id']) . " ORDER BY type, name";
$result = $db->query($sql);

// 生成书签文件头部
$output = '<!DOCTYPE NETSCAPE-Bookmark-file-1>
<!-- This is an automatically generated file.
     It will be read and overwritten.
     DO NOT EDIT! -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE>Bookmarks</TITLE>
<H1>Bookmarks</H1>
<DL><p>
    <DT><H3 ADD_DATE="' . time() . '" LAST_MODIFIED="' . time() . '">导出的书签</H3>
    <DL><p>
';

// 按分类组织书签
$bookmarks_by_type = array();
while ($row = mysql_fetch_assoc($result)) {
    $type = $row['type'];
    if (!isset($bookmarks_by_type[$type])) {
        $bookmarks_by_type[$type] = array();
    }
    $bookmarks_by_type[$type][] = $row;
}

// 生成书签内容
foreach ($bookmarks_by_type as $type => $bookmarks) {
    // 添加分类文件夹
    $output .= '        <DT><H3 ADD_DATE="' . time() . '" LAST_MODIFIED="' . time() . '">' . 
               htmlspecialchars($type) . '</H3>
        <DL><p>
';
    
    // 添加该分类下的所有书签
    foreach ($bookmarks as $bookmark) {
        $output .= '            <DT><A HREF="' . htmlspecialchars($bookmark['url']) . 
                  '" ADD_DATE="' . time() . '">' . htmlspecialchars($bookmark['name']) . '</A>
';
    }
    
    $output .= '        </DL><p>
';
}

// 关闭所有标签
$output .= '    </DL><p>
</DL><p>';

// 设置响应头
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="bookmarks_' . date('Y_m_d') . '.html"');
header('Content-Length: ' . strlen($output));

// 输出文件内容
echo $output;

// 关闭数据库连接
$db->close();
?> 