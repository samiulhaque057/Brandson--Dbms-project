<?php
require 'includes/db_Connection.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM cold_storages WHERE coldstorage_id = ?");
$stmt->execute([$id]);

header("Location: dashboard-template.php");
exit;
?>
