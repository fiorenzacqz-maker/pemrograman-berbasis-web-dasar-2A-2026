<?php
require 'auth.php';
require 'database.php';
if ($_SESSION['role'] !== 'admin') exit("Forbidden");

$pesan = '';

if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    if (!$username || !$password || strlen($username) < 3 || strlen($password) < 5) {
        $pesan = "Username minimal 3 karakter dan password minimal 5 karakter.";
    } else {
        $cek = $conn->prepare("SELECT id FROM users WHERE username=?");
        $cek->bind_param("s", $username);
        $cek->execute();
        if ($cek->get_result()->fetch_assoc()) $pesan = "Username sudah terdaftar!";
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $add = $conn->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
            $add->bind_param("sss", $username, $hash, $role);
            if ($add->execute()) $pesan = "success";
            else $pesan = "Gagal tambah user.";
        }
    }
}

if (isset($_POST['aksi']) && $_POST['aksi'] === 'edit') {
    $id = intval($_POST['id'] ?? 0);
    $username = htmlspecialchars(trim($_POST['username']));
    $role = $_POST['role'] === 'admin' ? 'admin':'user';
    if ($id && $username) {
    $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $role, $id);
        if ($stmt->execute()) $pesan = "updated";
        else $pesan = "Gagal update user!";
    }
}

if(isset($_POST['aksi']) && $_POST['aksi']==='reset'){
    $id = intval($_POST['id'] ?? 0);
    $password = $_POST['password'] ?? '';
    if ($id && strlen($password) >= 5) {
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $password, $id);
        if ($stmt->execute()) $pesan = "reset";
        else $pesan = "Gagal reset password!";
    } else {
        $pesan = "Password minimal 5 karakter!";
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $username = $conn->query("SELECT username FROM users WHERE id=$id")->fetch_assoc()['username'] ?? '';
    if ($username && $username !== $_SESSION['username']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i",$id);
        if ($stmt->execute()) $pesan = "deleted";
        else $pesan = "Gagal hapus user!";
    } else {
        $pesan = "Tidak bisa hapus user ini!";
    }
}

$res = $conn->query("SELECT id, username, ROLE AS role FROM users ORDER BY id");
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User | Rak Buku Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.min.css">
</head>
<body>
    <div class="container py-4">
        <h3 class="mb-4">Manajemen User</h3>
        <a href="admin.php" class="btn btn-secondary mb-3">&laquo; Kembali Dashboard</a>
        <button class="btn btn-primary mb-3 float-end" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah User</button>
        <div class="clearfix"></div>
        <?php if($pesan=='success'): ?>
            <div class="alert alert-success">User berhasil ditambahkan.</div>
        <?php elseif($pesan=='updated'): ?>
            <div class="alert alert-success">User berhasil diupdate.</div>
        <?php elseif($pesan=='reset'): ?>
            <div class="alert alert-success">Password berhasil direset.</div>
        <?php elseif($pesan=='deleted'): ?>
            <div class="alert alert-success">User berhasil dihapus.</div>
        <?php elseif($pesan && $pesan!='success'&&$pesan!='updated'&&$pesan!='reset'&&$pesan!='deleted'): ?>
            <div class="alert alert-danger"><?= $pesan ?></div>
        <?php endif; ?>
        <div class="table-responsive rounded shadow-sm">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; foreach($res as $user): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                          <span class="badge <?= $user['role']=='admin'?'bg-success':'bg-primary' ?>">
                            <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td>
                          <button class="btn btn-sm btn-warning" onclick="showEdit(<?= $user['id'] ?>,'<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>','<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>')">Edit</button>
                            <button class="btn btn-sm btn-secondary" onclick="showReset(<?= $user['id'] ?>,'<?= htmlspecialchars($user['username']) ?>')">Reset Password</button>
                            <?php if($user['username']!==$_SESSION['username']): ?>
                            <button class="btn btn-sm btn-danger" onclick="hapusUser(<?= $user['id'] ?>)">Hapus</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal fade" id="modalTambah" tabindex="-1">
      <div class="modal-dialog">
        <form class="modal-content" method="POST" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title">Tambah User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="aksi" value="tambah">
            <div class="mb-2">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required minlength="3">
            </div>
            <div class="mb-2">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required minlength="5">
            </div>
            <div class="mb-2">
              <label>Role</label>
              <select name="role" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
    <div class="modal fade" id="modalEdit" tabindex="-1">
      <div class="modal-dialog">
        <form class="modal-content" method="POST" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" id="editId" name="id">
            <div class="mb-2">
              <label>Username</label>
              <input type="text" id="editUsername" name="username" class="form-control" required minlength="3">
            </div>
            <div class="mb-2">
              <label>Role</label>
              <select id="editRole" name="role" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
    
    <div class="modal fade" id="modalReset" tabindex="-1">
      <div class="modal-dialog">
        <form class="modal-content" method="POST" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title">Reset Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="aksi" value="reset">
            <input type="hidden" id="resetId" name="id">
            <div class="mb-2">
              <label>Password Baru</label>
              <input type="password" name="password" class="form-control" required minlength="5">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Reset</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.3/dist/sweetalert2.min.js"></script>
    <script>
    function showEdit(id, user, role){
        document.getElementById('editId').value = id;
        document.getElementById('editUsername').value = user;
        document.getElementById('editRole').value = role;
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }
    function showReset(id, user){
        document.getElementById('resetId').value = id;
        new bootstrap.Modal(document.getElementById('modalReset')).show();
    }
    function hapusUser(id){
        Swal.fire({
            icon:'warning', title:'Hapus User?',
            text:'Yakin hapus user ini?',
            showCancelButton:true, confirmButtonText:'Hapus', cancelButtonText:'Batal'
        }).then((res)=>{
            if(res.isConfirmed){
                window.location = "admin_user.php?hapus="+id;
            }
        });
    }
    </script>
</body>
</html>