<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php'; // เชื่อมต่อฐานข้อมูล

if (!isset($_GET['action']) || !isset($_GET['student_id'])) {
    $_SESSION['error'] = 'ไม่พบการกระทำหรือรหัสนิสิตที่ร้องขอ';
    header('Location: dashboard.php');
    exit;
}

$action = $_GET['action'];
$student_id = $_GET['student_id'];

if ($action === 'complete') {
    // ทำเครื่องหมายว่าคิวทั้งหมดของนิสิตคนนี้เสร็จสิ้น
    $stmt = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE student_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "ดำเนินการเสร็จสิ้นสำหรับนิสิตรหัส: " . htmlspecialchars($student_id);
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการทำเครื่องหมายว่าเสร็จสิ้น: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
    
} elseif ($action === 'reject') {
    // ปฏิเสธคิวทั้งหมดของนิสิตคนนี้
    $stmt = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE student_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "ปฏิเสธการจองทั้งหมดสำหรับนิสิตรหัส: " . htmlspecialchars($student_id);
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการปฏิเสธการจอง: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();

} else {
    $_SESSION['error'] = 'การกระทำไม่ถูกต้อง';
}

// Redirect กลับไปที่หน้า view_details เพื่อแสดงผลการเปลี่ยนแปลง
header("Location: view_details.php?student_id=" . urlencode($student_id));
exit;

?>