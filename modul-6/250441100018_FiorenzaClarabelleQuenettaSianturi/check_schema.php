<?php
require 'database.php';

echo "Table: users\n";
$result = $conn->query("DESCRIBE users");
while ($row = $result->fetch_assoc()) {
    echo "  " . $row["Field"] . " -> " . $row["Type"] . "\n";
}

echo "\nData in users:\n";
$result = $conn->query("SELECT id, username FROM users LIMIT 3");
while ($row = $result->fetch_assoc()) {
    echo "  ID: " . $row["id"] . ", Username: " . $row["username"] . "\n";
}
?>
