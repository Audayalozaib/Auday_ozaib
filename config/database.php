<?php
// config/database.php

// تضمين مكتبة Autoload من Composer
require_once __DIR__ . '/../vendor/autoload.php';

// استخدام مكتبة dotenv لتحميل متغيرات .env
 $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
 $dotenv->load();

try {
    // جلب الرابط من متغيرات البيئة (سيعمل على Railway والمحلي)
    $db_url = getenv('DATABASE_URL');
    
    // إذا لم يكن DATABASE_URL موجوداً (مثلاً في XAMPP)، استخدم المتغيرات الأخرى
    if (!$db_url) {
        $db_host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $db_user = getenv('DB_USER');
        $db_pass = getenv('DB_PASS');
        
        // إنشاء اتصال PDO لـ MySQL (للاستخدام المحلي)
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    } else {
        // تحليل الرابط لقاعدة بيانات PostgreSQL (للاستخدام على Railway)
        $db_parts = parse_url($db_url);
        $host = $db_parts['host'];
        $port = $db_parts['port'];
        $dbname = ltrim($db_parts['path'], '/');
        $user = $db_parts['user'];
        $password = $db_parts['pass'];

        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    }
    
    $pdo->setAttribute(PDO::ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
