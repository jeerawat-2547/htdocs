<?php
session_start();
// แก้ไขให้ใช้ admin_logged_in เพื่อให้สอดคล้องกับ login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
require '../db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alt_text = $_POST['alt_text'];
    $link_url = $_POST['link_url'];
    $order_no = $_POST['order_no'];
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // แก้ไขเส้นทางการอัปโหลดให้สอดคล้องกันทั้งหมด
        $target_dir = "../uploads/"; 
        $file_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        $image_path = "uploads/" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบว่าเป็นไฟล์รูปภาพจริงหรือไม่
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // ตรวจสอบขนาดไฟล์
            if ($_FILES["image"]["size"] > 5000000) { // 5MB
                $error = "ขออภัย, ไฟล์ของคุณมีขนาดใหญ่เกินไป";
            } else {
                // อนุญาตเฉพาะบางนามสกุลไฟล์
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                    $error = "ขออภัย, อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น";
                } else {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        // อัปโหลดสำเร็จ
                    } else {
                        $error = "ขออภัย, เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
                    }
                }
            }
        } else {
            $error = "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ";
        }
    } else {
        $error = "กรุณาเลือกไฟล์รูปภาพ";
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO slides (image_path, alt_text, link_url, order_no) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $image_path, $alt_text, $link_url, $order_no);
        if ($stmt->execute()) {
            $_SESSION['message'] = "เพิ่มสไลด์สำเร็จแล้ว";
            // แก้ไข Redirect ไปยัง manage_slides.php
            header("Location: manage_slides.php");
            exit;
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสไลด์ใหม่</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 30px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #007bff; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .form-group button { background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .form-group button:hover { background-color: #218838; }
        .error { color: red; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .back-link { display: block; margin-top: 20px; text-align: center; color: #007bff; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>เพิ่มสไลด์ใหม่</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="add_slide.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">รูปภาพ:</label>
                <input type="file" name="image" id="image" required>
            </div>
            <div class="form-group">
                <label for="alt_text">ข้อความอธิบาย:</label>
                <input type="text" name="alt_text" id="alt_text">
            </div>
            <div class="form-group">
                <label for="link_url">ลิงก์ URL:</label>
                <input type="url" name="link_url" id="link_url">
            </div>
            <div class="form-group">
                <label for="order_no">ลำดับการแสดงผล:</label>
                <input type="number" name="order_no" id="order_no" value="0">
            </div>
            <div class="form-group">
                <button type="submit">เพิ่มสไลด์</button>
            </div>
        </form>
        <a href="manage_slides.php" class="back-link">กลับไปหน้าจัดการสไลด์</a>
    </div>
</body>
</html>