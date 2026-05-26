<?php
require 'auth.php';
require 'database.php';

if($_SESSION['role'] !== 'admin') exit("Forbidden");

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) exit("Invalid ID");

if($_SERVER['REQUEST_METHOD']=='POST') {

    $judul = htmlspecialchars(trim($_POST['judul']));
    $penulis = htmlspecialchars(trim($_POST['penulis']));
    $tahun_terbit = intval($_POST['tahun_terbit']);
    $kategori = htmlspecialchars(trim($_POST['kategori']));
    $sinopsis = trim($_POST['sinopsis'] ?? '');
    $stok = intval($_POST['stok']);

    $coverSql = "";
    $cover = null;
    // helper for resizing (same logic as tambah.php)
    function resize_and_save_local($srcPath, $destPath, $maxWidth = 1200) {
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
      $dst = imagecreatetruecolor($newW, $newH);
      if ($type === IMAGETYPE_PNG) { imagealphablending($dst, false); imagesavealpha($dst, true); $src = imagecreatefrompng($srcPath); }
      else { $src = imagecreatefromjpeg($srcPath); }
      if (!$src) return false;
      imagecopyresampled($dst, $src, 0,0,0,0, $newW, $newH, $width, $height);
      $dir = dirname($destPath); if (!is_dir($dir)) @mkdir($dir, 0755, true);
      $saved = false;
      if ($type === IMAGETYPE_PNG) { $saved = imagepng($dst, $destPath, 6); }
      else { $saved = imagejpeg($dst, $destPath, 85); }
      return $saved;
    }

    if (isset($_FILES['cover']) && $_FILES['cover']['tmp_name']) {
      $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
      if (in_array($ext, ['jpg','jpeg','png'])) {
        $cover = 'cover_' . uniqid() . '.' . $ext;
        $old = $conn->query("SELECT cover FROM buku WHERE id=".(int)$id)->fetch_assoc()['cover'] ?? '';
        if ($old && $old !== 'default.png' && is_file(__DIR__.'/img/'.$old)) @unlink(__DIR__.'/img/'.$old);
        resize_and_save_local($_FILES['cover']['tmp_name'], __DIR__.'/img/'.$cover, 1200);
        $coverSql = ", cover=?";
      }
    }

    $stmt = $conn->prepare(
        "UPDATE buku SET judul=?, penulis=?, tahun_terbit=?, kategori=?, sinopsis=?, stok=? $coverSql WHERE id=?"
    );
    if($coverSql){
      $stmt->bind_param("ssissisi", $judul, $penulis, $tahun_terbit, $kategori, $sinopsis, $stok, $cover, $id);
    } else {
      $stmt->bind_param("ssissii", $judul, $penulis, $tahun_terbit, $kategori, $sinopsis, $stok, $id);
    }
    $stmt->execute();
    header("Location: admin.php?msg=updated");
    exit;
}

$buku = $conn->query("SELECT * FROM buku WHERE id=$id")->fetch_assoc();
if(isset($_GET['modal'])):
?>
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Edit Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="mb-2">
          <label>Judul Buku</label>
          <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($buku['judul']) ?>">
        </div>
        <div class="mb-2">
          <label>Penulis</label>
          <input type="text" name="penulis" class="form-control" required value="<?= htmlspecialchars($buku['penulis']) ?>">
        </div>
        <div class="mb-2">
          <label>Tahun Terbit</label>
          <input type="number" name="tahun_terbit" min="1900" max="<?= date('Y') ?>" class="form-control" required value="<?= $buku['tahun_terbit'] ?>">
        </div>
        <div class="mb-2">
          <label>Kategori</label>
          <input type="text" name="kategori" class="form-control" required value="<?= htmlspecialchars($buku['kategori']) ?>">
        </div>
        <div class="mb-2">
          <label>Sinopsis</label>
          <textarea name="sinopsis" class="form-control" rows="4"><?= htmlspecialchars($buku['sinopsis'] ?? '') ?></textarea>
        </div>
        <div class="mb-2">
          <label>Stok</label>
          <input type="number" name="stok" min="1" class="form-control" required value="<?= $buku['stok'] ?>">
        </div>
        <div class="mb-2">
          <label>Cover Sekarang</label>
          <?php
            $existingCover = $buku['cover'] ?? '';
            $existingFile = $existingCover && is_file(__DIR__.'/img/'.$existingCover) ? 'img/'.rawurlencode($existingCover) : 'data:image/svg+xml;utf8,' . rawurlencode("<svg xmlns='http://www.w3.org/2000/svg' width='120' height='160'><rect width='100%' height='100%' fill='%23f0f0f0'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%23b0b0b0' font-size='14'>No cover</text></svg>");
          ?>
          <div class="mb-2"><img id="previewEdit" src="<?= $existingFile ?>" class="cover-thumb" alt="Preview saat ini"></div>
          <label>Cover Baru (opsional)</label>
          <input type="file" name="cover" id="editCoverInput" class="form-control" accept="image/*">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>
<script>
setTimeout(()=>{ new bootstrap.Modal(document.getElementById('modalEdit')).show(); },100);
// Preview handler for edit modal
(function(){
  const input = document.getElementById('editCoverInput');
  const prev = document.getElementById('previewEdit');
  if(input && prev){
    input.addEventListener('change', function(e){
      const f = this.files && this.files[0];
      if(f){ prev.src = URL.createObjectURL(f); }
    });
  }
})();
</script>
<?php endif ?>