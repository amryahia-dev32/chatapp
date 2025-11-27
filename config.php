<?php
// ضَع بيانات قاعدة البيانات هنا
define('DB_HOST', 'sql308.infinityfree.com'); // عدّل حسب لوحة التحكم الخاصة بك
define('DB_USER', 'if0_36336639');
define('DB_PASS', 'OpMP8lRUFg5ivZ');
define('DB_NAME', 'if0_36336639_mydb');


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die('فشل الاتصال بقاعدة البيانات: ' . $mysqli->connect_error);
}

// **تأكد أنك أزلت عبارة session_start() من هنا**
$mysqli->set_charset("utf8mb4");
?>