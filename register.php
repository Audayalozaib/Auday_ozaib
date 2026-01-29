<?php
// register.php

// تضمين ملف الاتصال
require_once 'config/database.php';

// متغيرات لعرض الرسائل
 $error = '';
 $success = '';

// إذا تم إرسال الفورم
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // جلب البيانات وتنظيفها
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // التحقق من الحقول
    if (empty($username) || empty($email) || empty($password)) {
        $error = "يرجى ملء جميع الحقول.";
    } elseif ($password !== $password_confirm) {
        $error = "كلمتا المرور غير متطابقتين.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح.";
    } else {
        try {
            // التحقق إذا كان المستخدم أو الإيميل موجود مسبقاً
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.";
            } else {
                // تشفير كلمة المرور (هذه هي الخطوة الأهم للأمان)
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // إدخال المستخدم الجديد في قاعدة البيانات
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash]);
                
                $success = "تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.";
                header("refresh:3;url=login.php"); // الانتقال لصفحة الدخول بعد 3 ثواني
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
    <title>إنشاء حساب جديد</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>إنشاء حساب جديد</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <input type="password" name="password_confirm" placeholder="تأكيد كلمة المرور" required>
            <button type="submit">إنشاء الحساب</button>
        </form>
        <p>لديك حساب بالفعل؟ <a href="login.php">سجل دخولك هنا</a></p>
    </div>
</body>
</html>
