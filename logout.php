<?php
// logout.php

// بدء الجلسة
session_start();

// تفريغ جميع متغيرات الجلسة
 $_SESSION = [];

// تدمير الجلسة
session_destroy();

// توجيه المستخدم لصفحة الدخول
header("Location: login.php");
exit();
