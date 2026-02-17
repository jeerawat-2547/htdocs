<?php
session_start();

// รับค่า queue จาก URL
$queue = $_GET['queue'] ?? null;

// ถ้าไม่มีค่า queue แสดงข้อความผิดพลาด
if (!$queue) {
    ?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
      <meta charset="UTF-8">
      <title>ไม่พบคิว</title>
    </head>
    <body>
      <h2>❌ ไม่พบข้อมูลลำดับคิว</h2>
      <p>กรุณาลองใหม่อีกครั้ง</p>
      <a href="../index.php">กลับหน้าแรก</a>
    </body>
    </html>
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จองสำเร็จ</title>
</head>
<body>
  <h2>✅ จองสำเร็จ!</h2>
  <p>ลำดับคิวของคุณคือ: <strong><?= htmlspecialchars($queue) ?></strong></p>
  <a href="../index.php">กลับหน้าแรก</a>
</body>
</html>
