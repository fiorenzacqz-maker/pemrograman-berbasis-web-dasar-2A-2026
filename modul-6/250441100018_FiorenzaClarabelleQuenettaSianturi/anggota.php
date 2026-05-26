<?php
require 'auth.php';
require 'database.php';

if ($_SESSION['role'] !== 'user') {
    header('Location: admin.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;

if (isset($_POST['beri_rating'], $_POST['id_buku'], $_POST['rating'])) {
    $id_buku = intval($_POST['id_buku']);
    $nilai = intval($_POST['rating']);
    if ($nilai >= 1 && $nilai <= 5) {
        $cek = $conn->query("SELECT id FROM rating WHERE id_user=$user_id AND id_buku=$id_buku");
        if ($cek->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO rating (id_user, id_buku, rating) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $id_buku, $nilai);
            $stmt->execute();
            $msg = "Terima kasih atas ratingnya!";
        } else {
            $msg = "Kamu sudah memberi rating!";
        }
    }
}

$buku = $conn->query("SELECT * FROM buku ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rak Buku | Anggota</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-primary px-4">
    <a class="navbar-brand text-white fw-bold">Rak Buku Digital</a>
    <span class="text-white me-3"><?= htmlspecialchars($_SESSION['username']) ?></span>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
</nav>
<div class="container py-4">
    <h3 class="mb-3">Daftar Buku</h3>
    <?php if (!empty($msg)): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif ?>
    <div class="table-responsive shadow rounded mb-4">
        <table class="table table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Cover</th>
                    <th>Sinopsis</th>
                    <th>Stok</th>
                    <th>Rating</th>
                    <th>Rating Kamu</th>
                </tr>
            </thead>
            <tbody>
            <?php $i=1; foreach($buku as $b): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($b['judul']) ?></td>
                    <td><?= htmlspecialchars($b['penulis']) ?></td>
                    <td><?= htmlspecialchars($b['kategori']) ?></td>
                    <?php
                        $coverFile = $b['cover'] ? __DIR__ . '/img/' . $b['cover'] : '';
                        if ($coverFile && is_file($coverFile)) {
                            $coverUrl = 'img/' . rawurlencode($b['cover']);
                        } else {
                            $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='60' height='80'><rect width='100%' height='100%' fill='%23f0f0f0'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%23b0b0b0' font-size='10'>No cover</text></svg>";
                            $coverUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
                        }
                    ?>
                    <td><img src="<?= $coverUrl ?>" alt="Cover <?= htmlspecialchars($b['judul']) ?>" class="cover-thumb"></td>
                    
                    <td>
                        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#sin<?= $b['id']?>">Lihat</button>
                        <div class="collapse mt-2" id="sin<?= $b['id']?>">
                            <div class="card card-body"><?= nl2br(htmlspecialchars($b['sinopsis'])); ?></div>
                        </div>
                    </td>
                    <td><?= $b['stok'] ?></td>
                    <td>
                        <?php
                        $avgQ = $conn->query("SELECT AVG(rating) as avg, COUNT(*) as cnt FROM rating WHERE id_buku={$b['id']}");
                        $avg = $avgQ->fetch_assoc();
                        echo $avg['cnt']>0 ? number_format($avg['avg'],1)."⭐ ({$avg['cnt']})" : 'Belum ada';
                        ?>
                    </td>
                    <td>
                        <?php
                        $cekRate = $conn->query("SELECT rating FROM rating WHERE id_user=$user_id AND id_buku={$b['id']}");
                        if ($cekRate->num_rows > 0) {
                            echo '<span class="badge bg-success">Rating Kamu: '.$cekRate->fetch_assoc()['rating'].'⭐</span>';
                        } else {
                            echo '<form method="POST" class="d-inline"><input type="hidden" name="id_buku" value="'.$b['id'].'">';
                            echo '<select name="rating" required class="form-select form-select-sm d-inline w-auto">';
                            echo '<option value="">Rate</option>';
                            for($r=1;$r<=5;$r++) echo '<option value="'.$r.'">'.$r.'⭐</option>';
                            echo '</select>
                                <button type="submit" name="beri_rating" class="btn btn-sm btn-secondary">Kirim</button>
                            </form>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>