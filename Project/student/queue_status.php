<?php
require 'db.php';

$student_id = $_GET['student_id'] ?? null;
$course_id = $_GET['course_id'] ?? null;

if (!$student_id || !$course_id) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

// คิวล่าสุดที่ได้รับบริการ (เช่น คนที่มี queue_number ต่ำสุดที่ยังไม่ได้ให้บริการ)
$latest = $conn->query("SELECT MIN(queue_number) AS current_queue 
                        FROM bookings 
                        WHERE course_id = $course_id AND served = 0");

$current = $latest->fetch_assoc()['current_queue'] ?? 0;

// คิวของผู้ใช้
$my = $conn->query("SELECT queue_number FROM bookings 
                    WHERE student_id = $student_id AND course_id = $course_id 
                    ORDER BY id DESC LIMIT 1");

$my_queue = ($my->num_rows > 0) ? $my->fetch_assoc()['queue_number'] : null;

echo json_encode([
    'current_queue' => $current,
    'your_queue' => $my_queue
]);
?>
