<?php
// config/database.php

 $host = 'localhost';
 $dbname = 'auth_system'; // اسم قاعدة البيانات
 $username = 'root'; // اسم المستخدم الافتراضي في XAMPP
 $password = ''; // كلمة المرور الافتراضية في XAMPP تكون فارغة

try {
    // إنشاء اتصال PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // ضع وضع الخطأ على Exception لالتقاط الأخطاء بسهولة
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // في حالة فشل الاتصال، اعرض رسالة خطأ
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
