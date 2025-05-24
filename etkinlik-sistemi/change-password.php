<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$new_password, $_SESSION['user_id']]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifre Değiştir</title>
</head>
<body>
    <h2>Yeni Şifre Belirleyin</h2>
    <form method="POST">
        <label for="new_password">Yeni Şifre:</label>
        <input type="password" name="new_password" id="new_password" required><br><br>
        <button type="submit">Şifreyi Güncelle</button>
    </form>
</body>
</html>
