<?php
session_start();
require 'database.php';
$error = '';

if (isset($_POST['login'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = "Username dan Password harus diisi!";
    } else {
        
        $stmt = $conn->prepare("SELECT id, username, PASSWORD AS password, ROLE AS role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();

        $userPassword = $user['password'] ?? null;
        $userRole = $user['role'] ?? null;

        if ($user && $userPassword && password_verify($password, $userPassword)) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $userRole;
            $_SESSION['user_id'] = $user['id'];
            if ($userRole === "admin") {
                header("Location: admin.php");
            } else {
                header("Location: anggota.php");
            }
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Rak Buku Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="login-bg">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow login-card">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Login</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required minlength="3" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required minlength="5">
                    </div>
                    <button name="login" type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <a href="lupa_password.php" class="btn btn-link d-block mt-2">Lupa Password?</a>
                <a href="register.php" class="btn btn-link d-block mt-2">Belum punya akun? Daftar</a>
            </div>
        </div>
    </div>
</body>
</html>