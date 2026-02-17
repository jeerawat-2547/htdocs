<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php'; // เชื่อมต่อฐานข้อมูล

if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
    die("Invalid student ID. กรุณาระบุ ID นิสิตที่ถูกต้อง");
}

$student_id = $_GET['student_id'];

// ดึงข้อมูลนิสิต
$stmt_student = $conn->prepare("SELECT id, student_code, name, phone, major FROM students WHERE id = ?");
if ($stmt_student === false) {
    die('Prepare failed for student details: ' . htmlspecialchars($conn->error));
}
$stmt_student->bind_param("i", $student_id);
$stmt_student->execute();
$result_student = $stmt_student->get_result();
$student_details = $result_student->fetch_assoc();
$stmt_student->close();

if (!$student_details) {
    die("ไม่พบข้อมูลนิสิตสำหรับ ID: " . htmlspecialchars($student_id));
}

// ดึงข้อมูลการจองทั้งหมดของนิสิตคนนี้
// รวมถึงข้อมูลรายวิชาและข้อมูลจากตาราง reservations
$stmt_bookings = $conn->prepare("
    SELECT
        b.id AS booking_id,
        b.queue_number,
        b.status AS booking_status,
        b.created_at AS booking_created_at,
        c.code AS course_code,
        c.name AS course_name,
        c.group_name,
        r.reason,
        r.other_reason,
        r.gpa,
        r.department_code,
        r.faculty,
        r.degree_status,
        r.created_at AS application_created_at
    FROM bookings b
    JOIN students s ON s.id = b.student_id
    JOIN courses c ON c.id = b.course_id
    LEFT JOIN reservations r ON r.bookings_id = b.id
    WHERE b.student_id = ?
    ORDER BY b.queue_number ASC, b.created_at ASC
");

if ($stmt_bookings === false) {
    die('Prepare failed for bookings details: ' . htmlspecialchars($conn->error));
}

$stmt_bookings->bind_param("i", $student_id);
$stmt_bookings->execute();
$result_bookings = $stmt_bookings->get_result();
$all_bookings = [];
while ($row = $result_bookings->fetch_assoc()) {
    $all_bookings[] = $row;
}
$stmt_bookings->close();

// ตรวจสอบสถานะโดยรวมของนิสิตคนนี้ หากมีคิวใดคิวหนึ่งยังเป็น 'pending' ก็จะแสดงปุ่ม 'เสร็จสิ้น'
$can_mark_done = false;
foreach ($all_bookings as $booking) {
    if ($booking['booking_status'] === 'pending') {
        $can_mark_done = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจอง - นิสิต: <?= htmlspecialchars($student_details['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(to bottom, #ffffffff, #ffffff, #e0f2f7);
            min-height: 100vh;
            padding: 20px;
        }
        .main-container {
            max-width: 900px;
            margin: 30px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
            font-weight: 700;
        }
        h3 {
            color: #0056b3;
            border-bottom: 2px solid #d7f01cff;
            padding-bottom: 10px;
            margin-top: 30px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        h5 {
            color: #0f85fcff;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .list-group-item {
            border-radius: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            background-color: #f9f8faff;
        }
        .status-completed {
            color: #0dd33cff;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .btn-success {
            padding: 12px 20px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-secondary {
            font-weight: 600;
            border-radius: 8px;
        }
        .info-label {
            font-weight: 500;
            color: #0d24f6ff;
        }
        .back-link { display: block; margin-top: 20px; text-align: center; }
        .back-link a { text-decoration: none; color: #328deeff; font-weight: bold; padding: 8px 15px; border: 1px solid #007bff; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        .back-link a:hover { background-color: #0011ffff; color: white; }
    </style>
</head>
<body>

<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <h2 class="text-center flex-grow-1 mb-0">รายละเอียดการจอง</h2>
        <div></div>
    </div>
    
    <div class="card p-4 mb-4">
        <h3 class="card-title">ข้อมูลนิสิต</h3>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <span class="info-label">รหัสนิสิต:</span> <?= htmlspecialchars($student_details['student_code']) ?>
            </li>
            <li class="list-group-item">
                <span class="info-label">ชื่อนิสิต:</span> <?= htmlspecialchars($student_details['name']) ?>
            </li>
            <li class="list-group-item">
                <span class="info-label">เบอร์โทร:</span> <?= htmlspecialchars($student_details['phone']) ?>
            </li>
            <li class="list-group-item">
                <span class="info-label">สาขา:</span> <?= htmlspecialchars($student_details['major']) ?>
            </li>
        </ul>
    </div>

    <h3>รายการจองทั้งหมด</h3>
    <?php if (!empty($all_bookings)): ?>
        <?php foreach ($all_bookings as $booking_item): ?>
            <div class="card p-3 mb-3">
                <h5 class="card-title">คิวที่ <?= htmlspecialchars($booking_item['queue_number']) ?></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <span class="info-label">รหัสวิชา:</span> <?= htmlspecialchars($booking_item['course_code']) ?>
                    </li>
                    <li class="list-group-item">
                        <span class="info-label">ชื่อวิชา:</span> <?= htmlspecialchars($booking_item['course_name']) ?>
                    </li>
                    <li class="list-group-item">
                        <span class="info-label">กลุ่มเรียน:</span> <?= htmlspecialchars($booking_item['group_name']) ?>
                    </li>
                    <li class="list-group-item">
                        <span class="info-label">วันที่/เวลาจอง:</span> <?= htmlspecialchars($booking_item['booking_created_at']) ?>
                    </li>
                    <li class="list-group-item">
                        <span class="info-label">สถานะ:</span> <span class="status-<?= strtolower(htmlspecialchars($booking_item['booking_status'])) ?>"><?= htmlspecialchars($booking_item['booking_status']) ?></span>
                    </li>
                </ul>

                <?php if (!empty($booking_item['reason']) || !empty($booking_item['gpa'])): ?>
                    <h5 class="mt-4">รายละเอียดการจอง</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <span class="info-label">เหตุผล:</span> <?= !empty($booking_item['reason']) ? nl2br(htmlspecialchars($booking_item['reason'])) : '-' ?>
                        </li>
                        <?php if (!empty($booking_item['other_reason'])): ?>
                            <li class="list-group-item">
                                <span class="info-label">เหตุผลอื่นๆ:</span> <?= nl2br(htmlspecialchars($booking_item['other_reason'])) ?>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item">
                            <span class="info-label">GPA:</span> <?= !empty($booking_item['gpa']) ? htmlspecialchars($booking_item['gpa']) : '-' ?>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label">รหัสภาควิชา:</span> <?= !empty($booking_item['department_code']) ? htmlspecialchars($booking_item['department_code']) : '-' ?>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label">คณะ:</span> <?= !empty($booking_item['faculty']) ? htmlspecialchars($booking_item['faculty']) : '-' ?>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label">สถานะการศึกษา:</span> <?= !empty($booking_item['degree_status']) ? htmlspecialchars($booking_item['degree_status']) : '-' ?>
                        </li>
                        <li class="list-group-item">
                            <span class="info-label">วันที่สมัคร:</span> <?= !empty($booking_item['application_created_at']) ? htmlspecialchars($booking_item['application_created_at']) : '-' ?>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center mt-4">ไม่พบรายการจองสำหรับนิสิตคนนี้</div>
    <?php endif; ?>

    <?php if ($can_mark_done): ?>
        <div class="d-flex justify-content-center gap-2 mt-4">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal" data-student-id="<?= htmlspecialchars($student_id) ?>">
                ทำเครื่องหมายว่าเสร็จสิ้น
            </button>
            
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" data-student-id="<?= htmlspecialchars($student_id) ?>">
                ปฏิเสธทั้งหมด
            </button>
        </div>
             
        </form>
        <div class="back-link">
        <a href="dashboard.php">กลับหน้าหลักผู้ดูแลระบบ</a>
    </div>
        <?php else: ?>
        <div class="alert alert-secondary text-center mt-4">นิสิตคนนี้ไม่มีคิวที่รอดำเนินการแล้ว</div>
        <div class="back-link">
        <a href="dashboard.php">กลับหน้าหลักผู้ดูแลระบบ</a>
    </div>
    <?php endif; ?>

</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">ยืนยันการดำเนินการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                คุณแน่ใจหรือไม่ว่าต้องการทำเครื่องหมายว่านิสิตคนนี้เสร็จสิ้นการดำเนินการทั้งหมดแล้ว?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <a id="confirmButton" href="#" class="btn btn-success">ยืนยัน</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">ยืนยันการปฏิเสธ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                คุณแน่ใจหรือไม่ว่าต้องการปฏิเสธการจองคิวของนิสิตคนนี้ทั้งหมด? การดำเนินการนี้ไม่สามารถย้อนกลับได้
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <a id="rejectButton" href="#" class="btn btn-danger">ยืนยันการปฏิเสธ</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript สำหรับ Modal "เสร็จสิ้น"
    var confirmModal = document.getElementById('confirmModal');
    confirmModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var studentId = button.getAttribute('data-student-id');
        var confirmButton = confirmModal.querySelector('#confirmButton');
        confirmButton.href = 'process_booking.php?action=complete&student_id=' + studentId;
    });

    // JavaScript สำหรับ Modal "ปฏิเสธทั้งหมด"
    var rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var studentId = button.getAttribute('data-student-id');
        var rejectButton = rejectModal.querySelector('#rejectButton');
        rejectButton.href = 'process_booking.php?action=reject&student_id=' + studentId;
    });
</script>

</body>
</html>