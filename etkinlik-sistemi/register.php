<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $admin_secret = $_POST['admin_secret'] ?? ''; // admin şifresi girilmişse al

    $is_admin = 0; // varsayılan kullanıcı

    // Admin girişi kontrolü
    if ($email === 'admin' || $email === 'admin@admin.com') {
        // Admin olmak için gizli şifre doğru mu kontrol et
        if ($admin_secret !== 'gizliAdminSifresi123') {
            echo "Admin kaydı için doğru yönetici şifresini girmeniz gerekiyor.";
            exit;
        }
        $email = 'admin@admin.com'; // sabit admin maili
        $is_admin = 1;
    }

    // Aynı e-posta ile daha önce kayıt yapılmış mı?
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo "Bu e-posta adresi zaten kayıtlı.";
        exit;
    }

    // Admin ise otomatik onayla, değilse onay beklesin
    $is_approved = ($is_admin === 1) ? 1 : 0;

    // Kayıt işlemi
    $stmt = $pdo->prepare("INSERT INTO users (email, password, is_admin, is_approved, must_change_password, first_login) VALUES (?, ?, ?, ?, 0, 0)");
    $stmt->execute([
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        $is_admin,
        $is_approved
    ]);

    echo "Kayıt başarılı!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Sayfası</title>
    <link rel="stylesheet" href="etkinlik.css">
    <script>
    function toggleAdminField() {
        var email = document.getElementById('email').value;
        var adminField = document.getElementById('admin-secret-container');
        if (email === 'admin' || email === 'admin@admin.com') {
            adminField.style.display = 'block';
        } else {
            adminField.style.display = 'none';
        }
    }
    </script>
</head>
<body>
    <div class="register-container">
        <h2>Kayıt Ol</h2>
        <form method="POST">
            <input type="text" id="email" name="email" placeholder="E-posta" required oninput="toggleAdminField()"><br>
            <input type="password" name="password" placeholder="Şifre" required><br>
            
            <div id="admin-secret-container" style="display:none;">
                <input type="password" name="admin_secret" placeholder="Yönetici Gizli Şifresi"><br>
            </div>

            <input type="submit" value="Kayıt Ol">
        </form>
    </div>
</body>
</html>
