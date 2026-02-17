<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php'; // เชื่อมต่อฐานข้อมูล (อยู่ในโฟลเดอร์แม่ของ admin)

// ดึงข้อมูลการจอง โดย GROUP BY student_id เพื่อรวมคิวของนิสิตคนเดียวกัน
$sql_bookings = "
    SELECT
        s.id AS student_id,
        s.student_code,
        s.name AS student_name,
        s.phone,
        s.major,
        MIN(b.queue_number) AS min_queue_number,
        GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS courses_booked,
        b.status AS booking_status,
        MAX(b.created_at) AS last_booking_date
    FROM bookings b
    JOIN students s ON s.id = b.student_id
    JOIN courses c ON c.id = b.course_id
    WHERE b.status = 'pending'
    GROUP BY s.id, s.student_code, s.name, s.phone, s.major
    ORDER BY min_queue_number ASC, last_booking_date ASC
";

$result_bookings = $conn->query($sql_bookings);

if ($result_bookings === false) {
    die('Error executing query: ' . htmlspecialchars($conn->error));
}

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
    <title>แดชบอร์ดผู้ดูแลระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 1200px; margin: 30px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; margin-bottom: 25px; }
        .header-links { text-align: right; margin-bottom: 20px; }
        .header-links a { margin-left: 15px; text-decoration: none; color: #007bff; font-weight: bold; padding: 8px 15px; border: 1px solid #007bff; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        .header-links a:hover { background-color: #007bff; color: white; }
        .header-links .logout-btn { background-color: #dc3545; color: white; border-color: #dc3545; }
        .header-links .logout-btn:hover { background-color: #c82333; }
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
    </style>
</head>
<body>

<div class="container">
    <h2>แดชบอร์ดผู้ดูแลระบบ</h2>
    <div class="header-links">
        <a href="add_course.php">เพิ่มรายวิชา</a>
        <a href="slides.php">จัดการสไลด์</a>
        <a href="../index.php" class="logout-btn">ออกจากระบบ</a>
    </div>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <h3>รายการคำขอสำรองที่นั่ง (คิวปัจจุบัน)</h3>
    <table>
        <thead>
            <tr>
                <th>รหัสนิสิต</th>
                <th>ชื่อนิสิต</th>
                <th>เบอร์โทร</th>
                <th>สาขา</th>
                <th>คิวแรก</th>
                <th>รายวิชาที่จอง (รวม)</th>
                <th>สถานะ</th>
                <th>ดูรายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_bookings->num_rows > 0): ?>
                <?php while($row = $result_bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['student_code']) ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['major']) ?></td>
                        <td><?= htmlspecialchars($row['min_queue_number']) ?></td>
                        <td><?= htmlspecialchars($row['courses_booked']) ?></td>
                        <td><?= htmlspecialchars($row['booking_status']) ?></td>
                        <td class="action-links">
                            <a href="view_details.php?student_id=<?= htmlspecialchars($row['student_id']) ?>">ดูรายละเอียด</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="no-records">ยังไม่มีรายการคำขอสำรองที่นั่งที่รอดำเนินการ</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>