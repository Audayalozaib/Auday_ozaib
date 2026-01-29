<?php
// home.php

// التأكد من بدء الجلسة
session_start();

// التحقق إذا لم يكن المستخدم مسجل دخوله، قم بتوجيهه لصفحة الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الصفحة الرئيسية</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>مرحباً بك، <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>هذه هي صفحتك الرئيسية. يمكنك رؤية هذه المحتوى فقط لأنك سجلت دخولك بنجاح.</p>
        <p><a href="logout.php">تسجيل الخروج</a></p>
    </div>
</body>
</html>
