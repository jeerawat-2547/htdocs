<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
require '../db.php'; // ตรวจสอบให้แน่ใจว่า path ไปยัง db.php ถูกต้อง

$message = '';
$error = '';
$code = ''; // กำหนดค่าเริ่มต้นเพื่อป้องกัน undefined variable notices
$name = '';
$group_name = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = trim($_POST['code']); // ลบช่องว่างหน้าหลัง
    $name = trim($_POST['name']);
    $group_name = trim($_POST['group_name']);

    if (empty($code) || empty($name) || empty($group_name)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // ตรวจสอบว่ารหัสวิชามีอยู่แล้วหรือไม่
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE code = ?");
        $check_stmt->bind_param("s", $code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_row();
        if ($row[0] > 0) {
            $error = "รหัสวิชานี้มีอยู่ในระบบแล้ว กรุณาใช้รหัสอื่น";
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (code, name, group_name) VALUES (?, ?, ?)");
            if ($stmt === false) {
                $error = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง: " . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("sss", $code, $name, $group_name);

                if ($stmt->execute()) {
                    $message = "เพิ่มรายวิชาสำเร็จ!";
                    // เคลียร์ค่าในฟอร์มหลังจากเพิ่มสำเร็จ
                    $code = '';
                    $name = '';
                    $group_name = '';
                } else {
                    $error = "เกิดข้อผิดพลาดในการเพิ่มรายวิชา: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มรายวิชาใหม่</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 30px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="number"], select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: 'Sarabun', sans-serif;
            font-size: 16px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        button:hover { background-color: #218838; }
        .message { color: green; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .error { color: red; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .back-link { display: block; margin-top: 20px; text-align: center; }
        .back-link a { text-decoration: none; color: #328deeff; font-weight: bold; padding: 8px 15px; border: 1px solid #007bff; border-radius: 5px; transition: background-color 0.3s, color 0.3s; }
        .back-link a:hover { background-color: #0011ffff; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2>เพิ่มรายวิชาใหม่</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="code">รหัสวิชา:</label>
        <input type="text" id="code" name="code" value="<?= htmlspecialchars($code) ?>" required><br>

        <label for="name">ชื่อวิชา:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br>

        <label for="group_name">กลุ่มเรียน:</label>
        <input type="text" id="group_name" name="group_name" value="<?= htmlspecialchars($group_name) ?>" required><br>
        <button type="submit">เพิ่มรายวิชา</button>
    </form>
    <div class="back-link">
        <a href="dashboard.php">กลับหน้าหลักผู้ดูแลระบบ</a>
    </div>
</div>

</body>
</html>