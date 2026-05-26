<?php
require 'database.php';

$tests = [
    ['admin', 'admin123'],
    ['user', 'user123'],
];

foreach ($tests as [$username, $password]) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    
    if ($user && $password === $user['password']) {
        echo "✓ LOGIN_OK: $username (password: $password)\n";
    } else {
        echo "✗ LOGIN_FAILED: $username\n";
        if ($user) {
            echo "  Got password: " . substr($user['password'], 0, 10) . "...\n";
        }
    }
}
?>
