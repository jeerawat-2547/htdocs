<?php
session_start();
// แก้ไขให้ใช้ admin_logged_in เพื่อให้สอดคล้องกับ login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require '../db.php';

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
    <title>ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(to bottom, #e0f2f7, #ffffff, #e0f2f7);
            min-height: 100vh;
            padding: 20px;
        }
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #000000ff;
            margin-bottom: 30px;
            font-weight: 700;
        }
        h3 {
            color: #0056b3;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .header-links {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-bottom: 20px;
        }
        .header-links .btn {
            font-weight: 600;
            border-radius: 8px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table thead th {
            background-color: #2f00ffff;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: #e0f2f7;
            cursor: pointer;
        }
        .message, .error {
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .no-records {
            text-align: center;
            color: #6c757d;
            padding: 20px;
        }
        .action-links a {
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center flex-grow-1 mb-0">หน้าหลักผู้ดูแลระบบ</h2>
    </div>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    

    <div class="d-flex justify-content-between align-items-center mb-1">
        <h3 class="text-center flex-grow-0 mb-0">รายการคำขอสำรองที่นั่ง (คิวปัจจุบัน)</h3>
        
        <div class="d-flex justify-content-between align-items-center mb-0">
            <div class="header-links">
            <a href="add_course.php" class="btn btn-primary">เพิ่มรายวิชา</a>
            <a href="manage_slides.php" class="btn btn-info text-white">จัดการสไลด์</a>
            <a href="../index.php" class="btn btn-danger">ออกจากระบบ</a>
            </div>
        </div>
    </div>


        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>รหัสนิสิต</th>
                    <th>ชื่อนิสิต</th>
                    <th>เบอร์โทร</th>
                    <th>สาขา</th>
                    <th>คิวแรก</th>
                    <th>รายวิชาที่จอง (รวม)</th>
                    <th>สถานะ</th>
                    <th>ดูรายละเอียด</th>
                <tr>
            </thead>
            <tbody>
                <?php if ($result_bookings->num_rows > 0): ?>
                    <?php while($row = $result_bookings->fetch_assoc()): ?>
                        <tr onclick="window.location='view_details.php?student_id=<?= htmlspecialchars($row['student_id']) ?>';" style="cursor: pointer;">
                            <td><?= htmlspecialchars($row['student_code']) ?></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['major']) ?></td>
                            <td><?= htmlspecialchars($row['min_queue_number']) ?></td>
                            <td><?= htmlspecialchars($row['courses_booked']) ?></td>
                            <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['booking_status']) ?></span></td>
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
    
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>