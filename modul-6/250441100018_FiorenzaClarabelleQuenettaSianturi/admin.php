<?php
require 'auth.php';
require 'database.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: anggota.php');
    exit;
}

$total_buku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$total_stok = $conn->query("SELECT SUM(stok) as stok FROM buku")->fetch_assoc()['stok'];

$resKat = $conn->query("SELECT DISTINCT kategori FROM buku");
$kategori = [];
while ($k = $resKat->fetch_assoc()) $kategori[] = $k['kategori'];

$perPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page-1)*$perPage;

$cariRaw = isset($_GET['search']) ? trim($_GET['search']) : '';
$cari = htmlspecialchars($cariRaw);
$filter = isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : '';

$where = [];
if ($cariRaw) {
  $cariSql = $conn->real_escape_string(strtolower($cariRaw));
  $where[] = "(LOWER(judul) LIKE '%$cariSql%' OR LOWER(penulis) LIKE '%$cariSql%')";
}
if ($filter) $where[] = "kategori = '$filter'";
$where_sql = $where ? "WHERE ".implode(" AND ", $where) : "";

$totalRows = $conn->query("SELECT COUNT(*) as total FROM buku $where_sql")->fetch_assoc()['total'];
$totalPages = ceil($totalRows/$perPage);
$buku = $conn->query("SELECT * FROM buku $where_sql ORDER BY id DESC LIMIT $perPage OFFSET $offset");
$recentRatings = $conn->query("SELECT r.rating, r.tanggal, u.username, b.judul FROM rating r JOIN users u ON u.id = r.id_user JOIN buku b ON b.id = r.id_buku ORDER BY r.tanggal DESC LIMIT 10");

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Rak Buku Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.min.css">
</head>
<body id="adminBody">
    <nav class="navbar navbar-expand-lg navbar-light bg-primary bg-gradient px-4 shadow mb-3 mt-2">
        <a class="navbar-brand text-white fw-bold" href="#">Rak Buku Digital <span class="badge bg-light text-primary ms-2">Admin</span></a>
        <div class="ms-auto">
            <span class="text-white me-3">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="admin_user.php" class="btn btn-warning btn-sm ms-2 text-dark">
                <i class="bi bi-people"></i> Manajemen User
            </a>
            <a href="logout.php" class="btn btn-danger btn-sm ms-2">Logout</a>
        </div>
    </nav>
    <div class="container py-3">
      
        <div class="row mb-3">
            <div class="col-md-6 col-lg-4 mb-2">
                <div class="card shadow-sm text-primary">
                    <div class="card-body">
                        <h5 class="fw-bold">Total Buku</h5>
                        <h2><?= $total_buku ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-2">
                <div class="card shadow-sm text-success">
                    <div class="card-body">
                        <h5 class="fw-bold">Total Stok</h5>
                        <h2><?= $total_stok ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4 mb-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <canvas id="chartStat"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <form class="row align-items-center mb-3" method="GET" id="searchForm">
          <div class="col-12 col-md-3 mb-2">
            <button class="btn btn-primary w-100" type="button" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Buku</button>
          </div>
          <div class="col-6 col-md-4 mb-2">
            <input type="text" class="form-control" id="searchBox" name="search" placeholder="Cari buku atau penulis..." value="<?= htmlspecialchars($cari) ?>">
          </div>
          <div class="col-6 col-md-3 mb-2">
            <select class="form-select" id="filterKategori" name="filter">
              <option value="">Semua Kategori</option>
              <?php foreach($kategori as $k): ?>
                <option value="<?= htmlspecialchars($k) ?>" <?= $filter==$k?'selected':'';?>><?= htmlspecialchars($k) ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="col-12 col-md-2 mb-2 d-grid gap-2">
            <button class="btn btn-success" type="submit">Cari</button>
          </div>
        </form>
        
        <div class="table-responsive shadow-sm rounded">
        <table class="table align-middle table-hover">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                  <th>Sinopsis</th>
                  <th>Rating User</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="dataBuku">
            <?php if ($buku->num_rows): $i=$offset+1; foreach($buku as $b): ?>
                <?php
                  $ratingQ = $conn->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_rating FROM rating WHERE id_buku=" . (int)$b['id']);
                  $ratingRow = $ratingQ ? $ratingQ->fetch_assoc() : ['avg_rating' => null, 'total_rating' => 0];
                  $sinopsis = trim($b['sinopsis'] ?? '');
                  
                  $coverFile = $b['cover'] ? __DIR__ . '/img/' . $b['cover'] : '';
                  if ($coverFile && is_file($coverFile)) {
                      $coverUrl = 'img/' . rawurlencode($b['cover']);
                  } else {
                      $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='120' height='160'><rect width='100%' height='100%' fill='%23f0f0f0'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%23b0b0b0' font-size='14'>No cover</text></svg>";
                      $coverUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
                  }
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><img src="<?= $coverUrl ?>" class="cover-thumb" alt="Cover <?= htmlspecialchars($b['judul']) ?>"></td>
                    <td><?= htmlspecialchars($b['judul']) ?></td>
                    <td><?= htmlspecialchars($b['penulis']) ?></td>
                    <td><?= htmlspecialchars($b['kategori']) ?></td>
                  <td>
                    <?php if ($sinopsis !== ''): ?>
                      <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#sinopsis<?= $b['id'] ?>">Lihat</button>
                      <div class="collapse mt-2" id="sinopsis<?= $b['id'] ?>">
                        <div class="small border rounded p-2 bg-light"><?= nl2br(htmlspecialchars($sinopsis)) ?></div>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ((int)$ratingRow['total_rating'] > 0): ?>
                      <?= number_format((float)$ratingRow['avg_rating'], 1) ?>⭐ <span class="text-muted">(<?= (int)$ratingRow['total_rating'] ?>)</span>
                    <?php else: ?>
                      <span class="text-muted">Belum ada</span>
                    <?php endif; ?>
                  </td>
                    <td><?= $b['tahun_terbit'] ?></td>
                    <td><?= $b['stok'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editBuku(<?= $b['id'] ?>)">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="hapusBuku(<?= $b['id'] ?>, '<?= htmlspecialchars($b['judul']) ?>')">Hapus</button>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="10" class="text-center">Tidak ada data buku!</td></tr>
            <?php endif ?>
            </tbody>
        </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <nav class="my-3">
            <ul class="pagination justify-content-center">
                <?php for($p=1;$p<=$totalPages;$p++): ?>
                    <li class="page-item <?= $p==$page?'active':'' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($cari) ?>&filter=<?= urlencode($filter) ?>"><?= $p ?></a>
                    </li>
                <?php endfor ?>
            </ul>
        </nav>
        <?php endif ?>

        <div class="card shadow-sm mb-4">
          <div class="card-header bg-white fw-bold">Rating dari User</div>
          <div class="card-body table-responsive">
            <?php if ($recentRatings && $recentRatings->num_rows): ?>
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Buku</th>
                    <th>Rating</th>
                    <th>Waktu</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentRatings as $rating): ?>
                    <tr>
                      <td><?= htmlspecialchars($rating['username']) ?></td>
                      <td><?= htmlspecialchars($rating['judul']) ?></td>
                      <td><?= (int)$rating['rating'] ?>⭐</td>
                      <td><?= htmlspecialchars($rating['tanggal']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="text-muted">Belum ada rating dari user.</div>
            <?php endif; ?>
          </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalTambah" tabindex="-1">
      <div class="modal-dialog">
        <form class="modal-content" id="formTambah" method="POST" enctype="multipart/form-data" action="tambah.php">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Buku</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-2">
              <label>Judul Buku</label>
              <input type="text" name="judul" class="form-control" required>
            </div>
            <div class="mb-2">
              <label>Penulis</label>
              <input type="text" name="penulis" class="form-control" required>
            </div>
            <div class="mb-2">
              <label>Tahun Terbit</label>
              <input type="number" name="tahun_terbit" min="1900" max="<?= date('Y') ?>" class="form-control" required>
            </div>
            <div class="mb-2">
              <label>Kategori</label>
              <input type="text" name="kategori" class="form-control" required>
            </div>
            <div class="mb-2">
              <label>Sinopsis</label>
              <textarea name="sinopsis" class="form-control" rows="4" placeholder="Tulis sinopsis buku..."></textarea>
            </div>
            <div class="mb-2">
              <label>Stok</label>
              <input type="number" name="stok" min="1" class="form-control" required>
            </div>
            <div class="mb-2">
              <label>Cover Buku (jpg/png, opsional)</label>
              <img id="previewTambah" src="" class="cover-thumb d-none mb-2" alt="Preview cover">
              <input type="file" name="cover" id="tambahCoverInput" class="form-control" accept="image/*">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
    
    <div id="modalEditWrap"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.min.js"></script>
    <script>

    new Chart(document.getElementById('chartStat'), {
      type: 'bar',
      data: { labels: ['Buku','Stok'], datasets: [{
        label: 'Statistik',
        data: [<?= $total_buku ?>, <?= $total_stok ?>],
        backgroundColor: ['#1976d2','#00b050']
      }]},
      options:{
        plugins:{legend:{display:false}},
        scales:{y:{beginAtZero:true}}
      }
    });

    document.getElementById('formTambah').onsubmit = function(e){
      for(let el of this.elements){ if(el.required && !el.value.trim()){ e.preventDefault(); Swal.fire('Lengkapi semua data!'); return false; } }
    };

    function editBuku(id){
      fetch('edit.php?id='+id+'&modal=1')
        .then(res=>res.text())
        .then(html=>{
          document.getElementById('modalEditWrap').innerHTML=html;
          new bootstrap.Modal(document.getElementById('modalEdit')).show();
        });
    }

    function hapusBuku(id, judul){
      Swal.fire({
        icon: 'warning', title: 'Yakin Hapus?',
        html: 'Buku <b>' + judul + '</b> akan dihapus.',
        showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
      }).then((res)=>{
        if(res.isConfirmed){
          window.location = "hapus.php?id="+id;
        }
      });
    }
    // Preview for tambah modal
    (function(){
      const tambahInput = document.getElementById('tambahCoverInput');
      const preview = document.getElementById('previewTambah');
      if(tambahInput && preview){
        tambahInput.addEventListener('change', function(e){
          const f = this.files && this.files[0];
          if(f){ preview.src = URL.createObjectURL(f); preview.classList.remove('d-none'); }
          else { preview.src=''; preview.classList.add('d-none'); }
        });
      }
    })();
    </script>
</body>
</html>