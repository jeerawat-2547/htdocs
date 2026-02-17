<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php'; // เชื่อมต่อฐานข้อมูล

$booking_id = $_GET['id'] ?? 0;

if ($booking_id) {
    // เริ่มต้น Transaction เพื่อให้การลบข้อมูลสัมพันธ์กันเป็นไปอย่างสมบูรณ์
    $conn->begin_transaction();
    try {
        // ลบข้อมูลในตาราง `reservations` ก่อน เนื่องจากมี Foreign Key ไป `bookings`
        // ตรวจสอบให้แน่ใจว่าตารางนี้มีอยู่และถูกต้อง (ใน SQL dump ของคุณคือ `reservations` ไม่ใช่ `applications`)
        $stmt_res = $conn->prepare("DELETE FROM reservations WHERE booking_id = ?");
        if ($stmt_res === false) {
            throw new Exception('Prepare failed for reservations delete: ' . $conn->error);
        }
        $stmt_res->bind_param("i", $booking_id);
        $stmt_res->execute();
        $stmt_res->close();

        // ลบข้อมูลในตาราง `bookings`
        $stmt_booking = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        if ($stmt_booking === false) {
            throw new Exception('Prepare failed for bookings delete: ' . $conn->error);
        }
        $stmt_booking->bind_param("i", $booking_id);
        $stmt_booking->execute();
        $stmt_booking->close();

        $conn->commit(); // ยืนยันการเปลี่ยนแปลง
        $_SESSION['message'] = "ลบรายการจองสำเร็จแล้ว.";
    } catch (Exception $e) {
        $conn->rollback(); // ยกเลิกการเปลี่ยนแปลงหากมีข้อผิดพลาด
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบรายการจอง: " . $e->getMessage();
        error_log("Error deleting booking (ID: $booking_id): " . $e->getMessage()); // บันทึกข้อผิดพลาดสำหรับดีบัก
    }
} else {
    $_SESSION['error'] = "ไม่พบ ID รายการจองที่ต้องการลบ.";
}

header("Location: dashboard.php");
exit;
?>