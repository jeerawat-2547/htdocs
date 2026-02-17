<?php
session_start();

$student_id_number = $_SESSION['last_student_id_number'] ?? 'ไม่พบข้อมูล';
$booking_id = $_SESSION['last_booking_id'] ?? 'ไม่พบข้อมูล';

// เคลียร์ session ที่เก็บข้อมูลชั่วคราวหลังจากแสดงผล
unset($_SESSION['last_student_id_number']);
unset($_SESSION['last_booking_id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองคิวสำเร็จ</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; text-align: center; }
        .container { background-color: #e6ffe6; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 500px; margin: 50px auto; border: 1px solid #4CAF50; }
        h2 { color: #4CAF50; }
        p { font-size: 1.1em; }
        .queue-number { font-size: 2em; color: #007bff; font-weight: bold; margin: 20px 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>✅ การสำรองที่นั่งสำเร็จ!</h2>
    <p>รหัสนิสิตของคุณ: <strong><?= htmlspecialchars($student_id_number) ?></strong></p>
    <p>หมายเลขคิวของคุณคือ:</p>
    <div class="queue-number"><?= htmlspecialchars($booking_id) ?></div>
    <p>โปรดจำหมายเลขคิวนี้ไว้เพื่อใช้ในการติดตามสถานะ</p>
    <br>
    <a href="reserve.php">สำรองที่นั่งเพิ่ม</a> |
    <a href="index.html">กลับหน้าแรก</a>
</div>

</body>
</html>