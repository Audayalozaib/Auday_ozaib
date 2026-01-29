<?php
// login.php

// تضمين ملف الاتصال
require_once 'config/database.php';

 $error = '';

// إذا تم إرسال الفورم
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "يرجى إدخال اسم المستخدم وكلمة المرور.";
    } else {
        try {
            // البحث عن المستخدم في قاعدة البيانات
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // التحقق من وجود المستخدم وصحة كلمة المرور
            if ($user && password_verify($password, $user['password_hash'])) {
                // كلمة المرور صحيحة! بدء الجلسة
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // التوجيه للصفحة الرئيسية
                header("Location: home.php");
                exit();
            } else {
                $error = "اسم المستخدم أو كلمة المرور غير صحيحة.";
            }
        } catch (PDOException $e) {
            $error = "حدث خطأ: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>تسجيل الدخول</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit">دخول</button>
        </form>
        <p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
    </div>
</body>
</html>
