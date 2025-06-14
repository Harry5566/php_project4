<?php
require_once './connect.php';

$sql = "SELECT id, password FROM members";
$stmt = $pdo->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $plainPassword = $row['password'];
    $hashed = password_hash($plainPassword, PASSWORD_ARGON2I);

    $update = $pdo->prepare("UPDATE members SET password = ? WHERE id = ?");
    $update->execute([$hashed, $id]);
}
?>