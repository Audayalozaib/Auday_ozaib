<?php
// config/database.php

try {
    // جلب معلومات الاتصال من متغيرات البيئة في Railway
    $db_url = getenv('DATABASE_URL');
    
    // تحليل الرابط للحصول على المكونات
    $db_parts = parse_url($db_url);
    
    $host = $db_parts['host'];
    $port = $db_parts['port'];
    $dbname = ltrim($db_parts['path'], '/');
    $user = $db_parts['user'];
    $password = $db_parts['pass'];

    // إنشاء اتصال PDO لـ PostgreSQL (الافتراضي في Railway)
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // في حالة فشل الاتصال، اعرض رسالة خطأ
    die("Could not connect to the database: " . $e->getMessage());
}
