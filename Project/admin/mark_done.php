<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php'; // เชื่อมต่อฐานข้อมูล

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid booking ID.";
    header("Location: dashboard.php");
    exit;
}

$booking_id = $_GET['id'];

$conn->begin_transaction(); // เริ่มต้น Transaction เพื่อให้แน่ใจว่าทุกอย่างสำเร็จหรือล้มเหลวพร้อมกัน

try {
    // 1. อัปเดตสถานะของรายการจองในตาราง bookings
    $stmt_bookings = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
    $stmt_bookings->bind_param("i", $booking_id);
    if (!$stmt_bookings->execute()) {
        throw new Exception("Error updating booking status: " . $stmt_bookings->error);
    }
    $stmt_bookings->close();

    // 2. อัปเดตสถานะในตาราง reservations ที่เชื่อมโยงกับ booking_id นี้
    // ตรวจสอบให้แน่ใจว่าคอลัมน์สำหรับเชื่อมโยงใน reservations คือ 'bookings_id'
    $stmt_reservations = $conn->prepare("UPDATE reservations SET status = 'completed' WHERE bookings_id = ?");
    $stmt_reservations->bind_param("i", $booking_id);
    if (!$stmt_reservations->execute()) {
        throw new Exception("Error updating reservation status: " . $stmt_reservations->error);
    }
    $stmt_reservations->close();

    $conn->commit(); // ยืนยันการเปลี่ยนแปลงทั้งหมด
    $_SESSION['message'] = "รายการจองและรายละเอียดถูกทำเครื่องหมายว่า 'เสร็จสิ้น' แล้ว.";

} catch (Exception $e) {
    $conn->rollback(); // ยกเลิกการเปลี่ยนแปลงทั้งหมดหากมีข้อผิดพลาด
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการทำเครื่องหมายรายการจอง: " . $e->getMessage();
}

$conn->close();

header("Location: dashboard.php"); // เมื่อทำเสร็จสิ้นให้กลับไปหน้า Dashboard
exit;
?>