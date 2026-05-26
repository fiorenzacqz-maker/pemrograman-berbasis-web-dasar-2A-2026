<?php
require 'database.php';
session_start();
$error = '';
$success = '';

if (isset($_POST['reset'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$username || !$password || !$password2) {
        $error = "Semua field wajib diisi!";
    } elseif (strlen($username) < 3 || strlen($password) < 5) {
        $error = "Username minimal 3 & password 5 karakter!";
    } elseif ($password !== $password2) {
        $error = "Password tidak sama!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $cek = $stmt->get_result()->fetch_assoc();

        if (!$cek) {
            $error = "Username tidak ditemukan!";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $stmt2->bind_param("ss", $passwordHash, $username);
            if($stmt2->execute()) {
                $success = "Password berhasil di-reset. Silakan login!";
            } else {
                $error = "Gagal reset password, coba lagi!";
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password | Rak Buku Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="login-bg">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow login-card">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Reset Password</h3>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                    <div class="text-center"><a href="login.php" class="btn btn-primary mt-2">Login</a></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if (!$success): ?>
                <form method="POST" id="resetForm" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
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
                    <button name="reset" type="submit" class="btn btn-primary w-100">Reset Password</button>
                    <a href="login.php" class="btn btn-link d-block mt-2">Kembali ke Login</a>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    document.getElementById("resetForm")?.addEventListener("submit",function(e){
        let x=0;
        for(let el of this.elements) if(el.hasAttribute("required") && !el.value.trim()) x++;
        if(x) { e.preventDefault(); alert("Semua field harus diisi!"); }
    });
    </script>
</body>
</html>