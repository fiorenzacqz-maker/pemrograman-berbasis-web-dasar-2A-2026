<?php
require 'database.php';

$updates = [
    ['admin', 'admin123'],
    ['user', 'user123']
];

foreach ($updates as [$username, $password]) {
    $stmt = $conn->prepare("UPDATE users SET PASSWORD=? WHERE username=?");
    $stmt->bind_param("ss", $password, $username);
    $stmt->execute();
}

echo "Updated " . count($updates) . " users with plain text passwords\n";
$conn->close();
?>
