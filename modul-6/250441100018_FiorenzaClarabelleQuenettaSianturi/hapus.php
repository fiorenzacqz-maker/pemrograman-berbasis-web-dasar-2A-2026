<?php
require 'auth.php';
require 'database.php';

if($_SESSION['role'] !== 'admin') exit("Forbidden");

$id = intval($_GET['id'] ?? 0);
if ($id) {
    $get = $conn->query("SELECT cover FROM buku WHERE id=$id")->fetch_assoc();
    if ($get && $get['cover'] && $get['cover'] !== 'default.png') {
        @unlink('img/'.$get['cover']);
    }
    $stmt = $conn->prepare("DELETE FROM buku WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: admin.php?msg=deleted");
exit;
?>