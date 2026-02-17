<?php
session_start();
session_unset(); // ล้างตัวแปร session ทั้งหมด
session_destroy(); // ทำลาย session
header("Location: login.php"); // ส่งกลับไปหน้า login
exit;
?>