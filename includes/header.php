<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="top-nav">
    <div class="nav-left">
        <a href="index.php" class="nav-logo">
            <i class="ri-compass-3-line"></i>
            <span>会会导航</span>
        </a>
    </div>
    <div class="nav-right">
        <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
            <i class="ri-home-4-line"></i>
            <span>首页</span>
        </a>
        <a href="categories.php" class="<?php echo $current_page === 'categories.php' ? 'active' : ''; ?>">
            <i class="ri-folder-settings-line"></i>
            <span>栏目管理</span>
        </a>
        <a href="import_bookmarks.php" class="<?php echo $current_page === 'import_bookmarks.php' ? 'active' : ''; ?>">
            <i class="ri-bookmark-line"></i>
            <span>导入书签</span>
        </a>
        <a href="export_bookmarks.php" class="<?php echo $current_page === 'export_bookmarks.php' ? 'active' : ''; ?>">
            <i class="ri-download-cloud-line"></i>
            <span>导出书签</span>
        </a>
    </div>
</header> 