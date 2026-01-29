<?php
// setup.php - ملف مؤقت لإنشاء الجدول

require_once 'config/database.php';

echo "جاري محاولة إنشاء جدول users...<br>";

try {
    $sql = "CREATE TABLE users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $pdo->exec($sql);

    echo "<strong>نجاح!</strong> تم إنشاء جدول 'users' بنجاح.<br>";
    echo "<strong>مهم جداً:</strong> احذف هذا الملف (setup.php) فوراً من مشروعك على GitHub.";

} catch (PDOException $e) {
    // إذا كان الجدول موجوداً مسبقاً، سيظهر خطأ، وهذا طبيعي
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "<strong>معلومات:</strong> جدول 'users' موجود بالفعل. لا داعي للقلق.";
    } else {
        die("<strong>خطأ:</strong> لم يتم إنشاء الجدول. " . $e->getMessage());
    }
}
?>
