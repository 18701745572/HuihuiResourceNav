<?php
// 获取所有分类
$sql = "SELECT * FROM categories WHERE user_id = " . intval($_SESSION['user_id']) . " ORDER BY id DESC";
$categories_result = $db->query($sql);
$categories = array();
while ($row = mysql_fetch_assoc($categories_result)) {
    $categories[] = $row;
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="logo-link">
            <i class="ri-compass-3-line"></i>
            <span>会会导航</span>
        </a>
    </div>
    <ul class="nav-menu">
        <li>
            <a href="index.php" class="<?php echo empty($current_category) ? 'active' : ''; ?>">
                <i class="ri-apps-line"></i>
                <span>全部资源</span>
            </a>
        </li>
        <?php foreach ($categories as $category): ?>
        <li>
            <a href="?category=<?php echo urlencode($category['name']); ?>" 
               class="<?php echo $current_category === $category['name'] ? 'active' : ''; ?>">
                <i class="<?php echo htmlspecialchars($category['icon']); ?>"></i>
                <span><?php echo htmlspecialchars($category['name']); ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div> 