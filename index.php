<?php
// index.php - الصفحة الرئيسية الجديدة (تسجيل الدخول وإنشاء الحساب)

// تضمين ملف الاتصال
require_once 'config/database.php';

// بدء الجلسة للتحقق إذا كان المستخدم مسجل دخوله بالفعل
session_start();

// إذا كان المستخدم مسجل دخوله، قم بتوجيهه للصفحة الداخلية
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// متغيرات لعرض الرسائل
 $error = '';
 $success = '';

// --- معالجة طلب إنشاء الحساب ---
if (isset($_POST['register'])) {
    $username = trim($_POST['reg_username']);
    $email = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $password_confirm = $_POST['reg_password_confirm'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "يرجى ملء جميع الحقول في إنشاء الحساب.";
    } elseif ($password !== $password_confirm) {
        $error = "كلمتا المرور غير متطابقتين.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash]);
                $success = "تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.";
            }
        } catch (PDOException $e) {
            $error = "حدث خطأ: " . $e->getMessage();
        }
    }
}

// --- معالجة طلب تسجيل الدخول ---
if (isset($_POST['login'])) {
    $username = trim($_POST['login_username']);
    $password = $_POST['login_password'];

    if (empty($username) || empty($password)) {
        $error = "يرجى إدخال اسم المستخدم وكلمة المرور.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .main-container {
            display: flex;
            width: 90%;
            max-width: 900px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            min-height: 500px;
        }

        .form-container {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-container h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container input {
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-container input:focus {
            border-color: #667eea;
            outline: none;
        }

        .form-container button {
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            background-color: #667eea;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container button:hover {
            background-color: #5a67d8;
        }

        .login-container {
            background-color: #f7f7f7;
        }

        .register-container {
            background-color: #ffffff;
        }

        .error, .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .error { background-color: #f8d7da; color: #721c24; }
        .success { background-color: #d4edda; color: #155724; }

        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                width: 95%;
            }
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- قسم تسجيل الدخول -->
    <div class="form-container login-container">
        <h2>أهلاً بعودتك</h2>
        <p>قم بتسجيل الدخول لحسابك</p>
        <?php if ($error && isset($_POST['login'])): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php" method="post">
            <input type="hidden" name="login" value="1">
            <input type="text" name="login_username" placeholder="اسم المستخدم" required>
            <input type="password" name="login_password" placeholder="كلمة المرور" required>
            <button type="submit">دخول</button>
        </form>
    </div>

    <!-- قسم إنشاء الحساب -->
    <div class="form-container register-container">
        <h2>إنشاء حساب جديد</h2>
        <p>انضم إلينا اليوم</p>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if ($error && isset($_POST['register'])): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="index.php" method="post">
            <input type="hidden" name="register" value="1">
            <input type="text" name="reg_username" placeholder="اسم المستخدم" required>
            <input type="email" name="reg_email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="reg_password" placeholder="كلمة المرور" required>
            <input type="password" name="reg_password_confirm" placeholder="تأكيد كلمة المرور" required>
            <button type="submit">إنشاء الحساب</button>
        </form>
    </div>
</div>

</body>
</html>
