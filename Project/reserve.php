<?php
session_start(); // เพิ่ม session_start() เพื่อให้สามารถใช้ session ได้
require '../db.php'; // ตรวจสอบพาธนี้ให้แน่ใจ ถ้า db.php อยู่โฟลเดอร์เดียวกันกับ reserve.php ให้เปลี่ยนเป็น 'db.php'

// ตรวจสอบว่ามีข้อมูลถูกส่งมาทาง POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $student_id_number = $_POST['student_id_number']; // แก้ไขตรงนี้: student_code -> student_id_number
    $student_name = $_POST['name'];                   // ชื่อ
    $phone = $_POST['phone'];
    $major = $_POST['major'];

    // เตรียมคำสั่ง SQL สำหรับแทรกข้อมูลนักศึกษา
    $stmt = $conn->prepare("INSERT INTO students (student_id_number, name, phone, major) VALUES (?, ?, ?, ?)"); // แก้ไขตรงนี้: student_code -> student_id_number
    $stmt->bind_param("ssss", $student_id_number, $student_name, $phone, $major); // ใช้ตัวแปรที่รับมา

    // ตรวจสอบว่าการ execute สำเร็จหรือไม่
    if ($stmt->execute()) {
        $id = $stmt->insert_id; // ได้ ID ของนักศึกษาที่เพิ่งแทรก
        $_SESSION['student_id'] = $id; // เก็บ student_id ไว้ใน session
        header("Location: step2.php?student_id=$id"); // ส่งต่อไปยังหน้าถัดไป
        exit;
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        // คุณอาจต้องการบันทึก error นี้ลงใน log ด้วย
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลนิสิต</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { background-color: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>

<form method="post">
    <h2>ข้อมูลนิสิต</h2>
    <label for="student_id_number">รหัสนิสิต:</label>
    <input type="text" name="student_id_number" id="student_id_number" pattern="\d{11}" maxlength="11" required placeholder="กรอกรหัสนิสิต 11 หลัก"><br>

    <label for="name">ชื่อ-นามสกุล:</label>
    <input type="text" name="name" id="name" required placeholder="กรอกชื่อ-นามสกุล"><br>

    <label for="phone">เบอร์โทร:</label>
    <input type="text" name="phone" id="phone" pattern="[0-9]{10}" maxlength="10" required placeholder="กรอกเบอร์โทร 10 หลัก"><br>

    <label for="major">สาขา:</label>
    <select name="major" id="major" required>
        <option value="">เลือกสาขา</option>
        <option value="Accounting">สาขาบัญชีบัณฑิต</option>
        <option value="Business Economics">สาขาเศรษฐศาสตร์ธุรกิจ</option>
        <option value="Business Computer Science">สาขาวิชาคอมพิวเตอร์ธุรกิจ</option>
        <option value="Modern Management">สาขาการจัดการสมัยใหม่</option>
        <option value="International Business">สาขาธุรกิจระหว่างประเทศ</option>
        <option value="Marketing">สาขาการตลาด</option>
        <option value="Financial Management">สาขาการบริหารการเงิน</option>
        <option value="Digital Business and Information Systems">สาขาธุรกิจดิจิทัลและระบบสารสนเทศ</option>
        <option value="Modern Entrepreneurship">สาขาการเป็นผู้ประกอบการธุรกิจสมัยใหม่</option>
    </select><br>

    <button type="submit">ถัดไป</button>
</form>

</body>
</html>