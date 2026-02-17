<?php
session_start();

$queue = $_GET['queue'] ?? null;
$already = $_GET['already'] ?? null;

// ถ้าไม่มีค่าใดเลย แสดงว่ามีการเข้าหน้านี้โดยตรง
if (!$queue && !$already) {
    header("Location: ../index.php");
    exit;
}

// อย่าลบ session ทิ้งทันที เพื่อให้ยังใช้ในหน้า index.php ได้
// unset($_SESSION['user_queue']);
if (isset($_SESSION['user_queue'])) {
    unset($_SESSION['user_queue']);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ลำดับการจองที่นั่ง</title>
  <style>
    body {
      font-family: 'Sarabun', sans-serif;
      background-color: #f9f9f9;
      color: #333;
      text-align: center;
      padding: 60px 20px;
    }
    h1 {
      font-size: 2.4rem;
      margin-bottom: 30px;
      color: #0071e3;
    }
    .message {
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #d10000;
    }
    .queue-number {
      font-size: 2.2rem;
      font-weight: bold;
      color: #0071e3;
      margin-bottom: 30px;
    }
    .btn {
      display: inline-block;
      padding: 12px 25px;
      background-color: #0071e3;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
    }
    .btn:hover {
      background-color: #005bb5;
    }
  </style>
</head>
<body>

  <h1>ลำดับการจองที่นั่ง</h1>

  <?php if ($already): ?>
    <div class="message">คุณได้ทำการสำรองที่นั่งในรายวิชานี้ไปแล้ว</div>
  <?php elseif ($queue): ?>
    <div class="queue-number">ลำดับคิวของคุณคือ: <?= htmlspecialchars($queue) ?></div>
 
  <?php else: ?>
    <div class="message">ไม่พบลำดับคิว หรือคุณเข้าหน้านี้โดยตรง</div>
  <?php endif; ?>

  <a href="../index.php" class="btn">กลับหน้าหลัก</a>

</body>
<script>
function fetchQueueStatus() {
  fetch('get_queue_status.php')
    .then(response => response.json())
    .then(data => {
      document.getElementById('current-queue').textContent = data.current || 'ข้อผิดพลาด';
      document.getElementById('user-queue').textContent = data.user || 'ข้อผิดพลาด';
    })
    .catch(error => {
      document.getElementById('current-queue').textContent = 'ข้อผิดพลาด';
      document.getElementById('user-queue').textContent = 'ข้อผิดพลาด';
      console.error('Error fetching queue:', error);
    });
}

// เรียกตอนโหลด
fetchQueueStatus();

// อัปเดตทุก 5 วินาที
setInterval(fetchQueueStatus, 5000);
</script>

</html>
