<?php
require '../db.php';  // เชื่อมต่อฐานข้อมูล

$username = 'admin';      // ชื่อผู้ใช้เริ่มต้น
$plainPassword = 'admin123';  // รหัสผ่านเริ่มต้น

// เข้ารหัสรหัสผ่านก่อนเก็บลงฐานข้อมูล
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// เพิ่มข้อมูล admin ลงฐานข้อมูล
// ตรวจสอบว่า username นี้มีอยู่แล้วหรือไม่ เพื่อป้องกันการเพิ่มซ้ำ
$check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "ผู้ใช้ admin มีอยู่ในระบบแล้ว ไม่จำเป็นต้องสร้างใหม่<br>";
} else {
    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "เพิ่ม admin เรียบร้อยแล้ว ชื่อ: <b>" . htmlspecialchars($username) . "</b> รหัสผ่าน: <b>" . htmlspecialchars($plainPassword) . "</b><br>";
        echo "กรุณาลบรหัสผ่านเริ่มต้นนี้ออกทันทีหลังจากเข้าสู่ระบบครั้งแรกเพื่อความปลอดภัย";
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่ม admin: " . htmlspecialchars($stmt->error) . "<br>";
    }
    $stmt->close();
}
$check_stmt->close();

$conn->close();
?>