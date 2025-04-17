<?php
require_once 'config.php';
require_once 'includes/Database.php';

$db = new Database();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $sql = "DELETE FROM mylink WHERE id = " . $id;
    $db->query($sql);
}

header('Location: index.php');
exit;
?> 