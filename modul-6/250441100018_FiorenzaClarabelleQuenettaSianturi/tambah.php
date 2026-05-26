<?php
require 'auth.php';
require 'database.php';

if($_SESSION['role'] !== 'admin') exit("Forbidden");

$judul = htmlspecialchars(trim($_POST['judul'] ?? ''));
$penulis = htmlspecialchars(trim($_POST['penulis'] ?? ''));
$tahun_terbit = intval($_POST['tahun_terbit'] ?? 0);
$kategori = htmlspecialchars(trim($_POST['kategori'] ?? ''));
$sinopsis = trim($_POST['sinopsis'] ?? '');
$stok = intval($_POST['stok'] ?? 0);
$cover = 'default.png';

// Helper: resize uploaded image and save to destination path
function resize_and_save($srcPath, $destPath, $maxWidth = 1200) {
    if (!file_exists($srcPath)) return false;
    $info = @getimagesize($srcPath);
    if (!$info) { return move_uploaded_file($srcPath, $destPath); }
    list($width, $height, $type) = $info;
    $newW = $width;
    $newH = $height;
    if ($width > $maxWidth) {
        $newW = $maxWidth;
        $newH = (int)($height * ($newW / $width));
    }
    // Create destination image
    $dst = imagecreatetruecolor($newW, $newH);
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $src = imagecreatefrompng($srcPath);
    } else {
        $src = imagecreatefromjpeg($srcPath);
    }
    if (!$src) return false;
    imagecopyresampled($dst, $src, 0,0,0,0, $newW, $newH, $width, $height);
    // Ensure destination dir
    $dir = dirname($destPath);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $saved = false;
    if ($type === IMAGETYPE_PNG) {
        $saved = imagepng($dst, $destPath, 6);
    } else {
        $saved = imagejpeg($dst, $destPath, 85);
    }
    imagedestroy($src);
    imagedestroy($dst);
    return $saved;
}

if (isset($_FILES['cover']) && $_FILES['cover']['tmp_name']) {
    $namafile = $_FILES['cover']['name'];
    $tmp = $_FILES['cover']['tmp_name'];
    $ext = strtolower(pathinfo($namafile, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png'])) {
        $cover = 'cover_' . uniqid() . '.' . $ext;
        resize_and_save($tmp, __DIR__ . '/img/' . $cover, 1200);
    }
}

$stmt = $conn->prepare("INSERT INTO buku (judul, penulis, tahun_terbit, kategori, sinopsis, stok, cover) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssissis", $judul, $penulis, $tahun_terbit, $kategori, $sinopsis, $stok, $cover);
if ($stmt->execute()) {
    header("Location: admin.php?msg=success");
} else {
    header("Location: admin.php?msg=error");
}
exit;
?>