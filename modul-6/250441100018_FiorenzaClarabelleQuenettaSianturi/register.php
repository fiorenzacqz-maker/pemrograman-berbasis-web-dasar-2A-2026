<?php
require 'database.php';
session_start();
$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if(!$username || !$password || !$password2) {
        $error = "Semua field wajib diisi!";
    } elseif(strlen($username) < 3 || strlen($password) < 5) {
        $error = "Username minimal 3 & password 5 karakter!";
    } elseif($password !== $password2) {
        $error = "Password tidak sama!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $cek = $stmt->get_result()->fetch_assoc();

        if($cek) {
            $error = "Username sudah terdaftar!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $passwordHash, $role);
            if($stmt->execute()) {
                $success = "Akun berhasil dibuat! Silakan login.";
            } else {
                $error = "Gagal mendaftar, coba lagi!";
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register | Rak Buku Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="login-bg">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow login-card">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Buat Akun Baru</h3>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" id="regForm" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username Baru</label>
                        <input type="text" class="form-control" name="username" required minlength="3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" required minlength="5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ulangi Password</label>
                        <input type="password" class="form-control" name="password2" required minlength="5">
                    </div>
                    <button name="register" type="submit" class="btn btn-primary w-100">Buat Akun</button>
                    <a href="login.php" class="btn btn-link d-block mt-2">Sudah punya akun? Login</a>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.getElementById("regForm").onsubmit = function(e){
        let f = this, x = 0;
        for(let el of f.elements) {
            if(el.hasAttribute("required") && !el.value.trim()) x++;
        }
        if(x) {
            e.preventDefault(); alert("Semua field harus diisi!");
        }
    };
    </script>
</body>
</html>