<?php
// เปิดการแสดงผลข้อผิดพลาดทั้งหมดสำหรับการพัฒนา
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เริ่มต้น Session และตรวจสอบสิทธิ์การเข้าถึง (สมมติว่ามีการล็อคอินแล้ว)
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// รวมไฟล์เชื่อมต่อฐานข้อมูล
require_once '../db.php';

// กำหนดตัวแปรสำหรับข้อความแจ้งเตือน
$message = '';
$message_type = '';

// ดึงข้อความแจ้งเตือนจาก session (กรณีถูกส่งมาจาก delete_slide.php หรือ add_slide.php)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// โค้ดสำหรับดึงรายการสไลด์ทั้งหมดมาแสดง
$sql_slides = "SELECT * FROM slides ORDER BY order_no ASC";
$result_slides = $conn->query($sql_slides);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสไลด์ - ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .wrapper { max-width: 1000px; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .table img { max-width: 150px; height: auto; }
        .header-links { text-align: right; margin-bottom: 20px; }
        .header-links a { margin-left: 15px; text-decoration: none; color: #007bff; font-weight: bold; padding: 8px 15px; border: 1px solid #007bff; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        .header-links a:hover { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="mb-4">จัดการสไลด์</h2>
        <div class="d-flex justify-content-between mb-3">
            <a href="add_slide.php" class="btn btn-primary">เพิ่มสไลด์ใหม่</a>
            <a href="dashboard.php" class="btn btn-secondary">กลับหน้าหลัก</a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">รูปภาพ</th>
                        <th scope="col">ลิงก์ URL</th>
                        <th scope="col">ลำดับ</th>
                        <th scope="col">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_slides && $result_slides->num_rows > 0): ?>
                        <?php while($row = $result_slides->fetch_assoc()): ?>
                            <tr>
                                <th scope="row"><?= htmlspecialchars($row['id']) ?></th>
                                <td><img src="../<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['alt_text']) ?>" class="img-fluid rounded"></td>
                                <td><?= htmlspecialchars($row['link_url']) ?></td>
                                <td><?= htmlspecialchars($row['order_no']) ?></td>
                                <td>
                                    <a href="edit_slide.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                    <a href="delete_slide.php?action=delete&id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสไลด์นี้?');">ลบ</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">ไม่พบข้อมูลสไลด์</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>