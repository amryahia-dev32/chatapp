<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$name = $mysqli->real_escape_string($_POST['name']);
$phone = $mysqli->real_escape_string($_POST['phone']);
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);


$stmt = $mysqli->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $name, $phone, $pass);

session_start(); // ابدأ الجلسة هنا قبل التوجيه
if ($stmt->execute()) {
$_SESSION['user_id'] = $stmt->insert_id;
$_SESSION['user_name'] = $name;
header('Location: index.php'); exit;
} else {
$error = 'خطأ أثناء التسجيل: ' . $mysqli->error;
}
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تسجيل - واتساب ويب تجريبي</title>
<style>
    body.auth-page {display:flex;justify-content:center;align-items:center;height:100vh;background:#111b21;color:#fff;font-family:Arial;}
    .auth-box {background:#202c33;padding:30px;border-radius:12px;text-align:center;width:300px;}
    .auth-box input, .auth-box button {width:100%;padding:10px;margin:10px 0;border:none;border-radius:8px;}
    .auth-box input {background:#111b21;color:#fff;}
    .auth-box button {background:#00a884;color:#fff;cursor:pointer;}
    .auth-box a {color:#00a884;text-decoration:none;}
    .auth-box label{display:block;text-align:right;margin-top:10px;}
    .error {color: #ff4d4d; background: #3a0000; padding: 10px; border-radius: 5px; margin-bottom: 10px;}
</style>
</head>
<body class="auth-page">
<div class="auth-box">
<h2>سجل حسابك</h2>
<?php if (!empty($error)) echo '<div class="error">'.htmlspecialchars($error).'</div>'; ?>
<form method="post">
<label>الاسم</label>
<input name="name" required>
<label>رقم الهاتف</label>
<input name="phone" required>
<label>كلمة المرور</label>
<input name="password" type="password" required>
<button type="submit">تسجيل</button>
</form>
<p>هل لديك حساب؟ <a href="login.php">تسجيل دخول</a></p>
</div>
</body>
</html>