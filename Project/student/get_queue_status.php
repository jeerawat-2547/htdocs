<?php
session_start();
require '../db.php'; // ตรวจสอบให้แน่ใจว่า path ไปยัง db.php ถูกต้อง

header('Content-Type: application/json'); // ระบุว่าเป็น JSON response

$response = [
    'current' => '-', // คิวปัจจุบันที่ระบบกำลังดำเนินการ (Lowest overall queue number)
    'user' => '-',    // คิวของนิสิตที่เข้าใช้งานล่าสุด
];

// 1. ดึง student_id ที่จองล่าสุดจาก session
$student_id = $_SESSION['last_booked_student_id'] ?? null;

// 2. หากมี student_id ใน session ให้พยายามค้นหาคิวต่ำสุดของนิสิตคนนั้น
if ($student_id) {
    // ดึงคิวที่ต่ำที่สุดของนิสิตคนนี้จากตาราง bookings (ถือว่าเป็นคิวแรกที่นิสิตคนนี้ต้องรอ)
    $stmt = $conn->prepare("SELECT MIN(queue_number) as user_min_queue FROM bookings WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row && $row['user_min_queue'] !== null) {
        $response['user'] = $row['user_min_queue'];
    }
}

//// 3. ดึง "คิวต่ำสุดในระบบ" ที่ยังมีสถานะเป็น 'pending' เท่านั้น
$stmt_current_queue = $conn->prepare("SELECT MIN(queue_number) as current_q FROM bookings WHERE status = 'pending'");
$stmt_current_queue->execute();
$result_current_queue = $stmt_current_queue->get_result();
$current_q_row = $result_current_queue->fetch_assoc();
$stmt_current_queue->close();
if ($current_q_row && $current_q_row['current_q'] !== null) {
    $response['current'] = $current_q_row['current_q'];
} else {
    // ถ้าไม่มีคิวในระบบเลย
    $response['current'] = 'ไม่มีคิว';
}

echo json_encode($response); // ส่งข้อมูลกลับไปเป็น JSON
$conn->close();
?>