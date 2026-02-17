<?php
session_start();
require '../db.php';

// ตรวจสอบว่ามี student_id ใน session หรือยัง
$student_id = $_SESSION['student_id'] ?? 0;
if ($student_id === 0) {
    // ถ้ายังไม่มี ให้ redirect กลับ
    header("Location: ../index.php");
    exit;
}

// เพิ่มข้อมูลลง queue
// หาลำดับคิวใหม่
$queue_query = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE course_id = '$course_id'");
$queue_row = $queue_query->fetch_assoc();
$queue_number = $queue_row['total'] + 1;

// เพิ่มข้อมูลการจอง
$sql_booking = "INSERT INTO bookings (student_id, course_id, queue_number)   VALUES ('$student_id', '$course_id', '$queue_number')";

$queue_id = $conn->insert_id; // ลำดับคิวที่เพิ่งเพิ่ม

// เก็บ queue_id ลง session (เผื่อใช้ในหน้าอื่น)
$_SESSION['user_queue'] = $queue_id;

// ส่งต่อไปหน้าแสดงผล พร้อมส่ง queue_id ไปด้วย
header("Location: success.php?queue=" . $queue_id);
exit;
