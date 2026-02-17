<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php';

$sql = "SELECT * FROM slides ORDER BY order_no ASC";
$result = $conn->query($sql);

$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสไลด์ - ผู้ดูแลระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 1000px; margin: 30px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; margin-bottom: 25px; }
        .header-links { text-align: right; margin-bottom: 20px; }
        .header-links a { margin-left: 15px; text-decoration: none; color: #007bff; font-weight: bold; padding: 8px 15px; border: 1px solid #007bff; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        .header-links a:hover { background-color: #007bff; color: white; }
        .header-links .add-btn { background-color: #28a745; color: white; border-color: #28a745; }
        .header-links .add-btn:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .action-links a { margin-right: 10px; text-decoration: none; color: #007bff; }
        .action-links a:hover { text-decoration: underline; }
        .action-links a.delete { color: #dc3545; }
        .action-links a.delete:hover { color: #c82333; }
        .message { color: green; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .error { color: red; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .no-records { text-align: center; color: #666; padding: 20px; }
        .slide-image { max-width: 150px; height: auto; display: block; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>จัดการสไลด์</h2>
        <div class="header-links">
            <a href="dashboard.php">กลับหน้าหลัก</a>
            <a href="add_slide.php" class="add-btn">เพิ่มสไลด์ใหม่</a>
            
        </div>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>รูปภาพ</th>
                    <th>ข้อความอธิบาย</th>
                    <th>ลิงก์ URL</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_no']) ?></td>
                            <td><img src="../<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['alt_text']) ?>" class="slide-image"></td>
                            <td><?= htmlspecialchars($row['alt_text']) ?></td>
                            <td><?= htmlspecialchars($row['link_url']) ?></td>
                            <td class="action-links">
                                <a href="edit_slide.php<?= htmlspecialchars($row['id']) ?>">แก้ไข</a>
                                <a href="delete_slide.php?id=<?= htmlspecialchars($row['id']) ?>" class="delete" onclick="return confirm('ยืนยันการลบสไลด์นี้หรือไม่?')">ลบ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-records">ยังไม่มีสไลด์ที่ตั้งค่าไว้</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>