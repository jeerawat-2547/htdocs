<?php
// เปิดการแสดงผลข้อผิดพลาดทั้งหมดสำหรับการพัฒนา
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// รวมไฟล์เชื่อมต่อฐานข้อมูล
require_once '../db.php';

// กำหนดตัวแปรสำหรับข้อความแจ้งเตือน
$message = '';
$message_type = '';

// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // หากไม่มี id ใน URL ให้ redirect ไปหน้าจัดการสไลด์
    // แก้ไข redirect ไปที่ manage_slides.php
    header('Location: manage_slides.php');
    exit;
} else {
    $id = $_GET['id'];
}

// โค้ดสำหรับดึงข้อมูลสไลด์ปัจจุบันมาแสดงในฟอร์ม
$sql = "SELECT * FROM slides WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $slide = $result->fetch_assoc();
} else {
    // หากไม่พบข้อมูลสไลด์ ให้ redirect ไปหน้าจัดการสไลด์
    // แก้ไข redirect ไปที่ manage_slides.php
    header('Location: manage_slides.php');
    exit;
}
$stmt->close();

// โค้ดสำหรับจัดการการส่งฟอร์มเพื่ออัปเดตข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_slide'])) {
    $id_to_update = $_POST['id'];
    $link_url = $_POST['link_url'];
    $alt_text = $_POST['alt_text'];
    $order_no = $_POST['order_no'];
    $current_image_path = $_POST['current_image_path'];
    $new_image_path = $current_image_path;

    // ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
        $upload_dir = '../uploads/';
        $file_name = uniqid('slide_') . '_' . basename($_FILES['image_file']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // ตรวจสอบประเภทไฟล์
        $check = getimagesize($_FILES['image_file']['tmp_name']);
        if ($check !== false) {
            // อนุญาตเฉพาะบางนามสกุลไฟล์
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = 'ขออภัย, อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น';
                $message_type = 'danger';
            } else {
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                    // ลบรูปภาพเก่าออก (ถ้าไม่ใช่รูปภาพเริ่มต้น)
                    if (file_exists($current_image_path) && strpos($current_image_path, 'default.jpg') === false) {
                        unlink($current_image_path);
                    }
                    $new_image_path = "uploads/" . $file_name;
                    $message = 'อัปเดตรูปภาพใหม่เรียบร้อยแล้ว';
                    $message_type = 'success';
                } else {
                    $message = 'ขออภัย, มีข้อผิดพลาดในการอัปโหลดไฟล์';
                    $message_type = 'danger';
                }
            }
        } else {
            $message = 'ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ';
            $message_type = 'danger';
        }
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    if ($message_type !== 'danger') {
        $sql_update = "UPDATE slides SET image_path = ?, link_url = ?, alt_text = ?, order_no = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssii", $new_image_path, $link_url, $alt_text, $order_no, $id_to_update);
        if ($stmt_update->execute()) {
            $message = 'อัปเดตสไลด์เรียบร้อยแล้ว';
            $message_type = 'success';
            
            // รีเฟรชข้อมูลสไลด์ที่แสดงในฟอร์มหลังจากอัปเดต
            $sql = "SELECT * FROM slides WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_to_update);
            $stmt->execute();
            $result = $stmt->get_result();
            $slide = $result->fetch_assoc();
            $stmt->close();
            
        } else {
            $message = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $stmt_update->error;
            $message_type = 'danger';
        }
        $stmt_update->close();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสไลด์ - ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .wrapper { max-width: 800px; margin: 40px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="mb-4">แก้ไขสไลด์</h2>
        <a href="manage_slides.php" class="btn btn-secondary mb-3">ย้อนกลับ</a>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (isset($slide)): ?>
            <form action="edit_slide.php?id=<?= htmlspecialchars($slide['id']) ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($slide['id']) ?>">
                <input type="hidden" name="current_image_path" value="<?= htmlspecialchars($slide['image_path']) ?>">

                <div class="mb-3">
                    <label for="current_image" class="form-label">รูปภาพปัจจุบัน:</label>
                    <img src="../<?= htmlspecialchars($slide['image_path']) ?>" alt="Current Slide Image" class="img-fluid rounded" style="max-height: 200px;">
                </div>
                
                <div class="mb-3">
                    <label for="image_file" class="form-label">อัปโหลดรูปภาพใหม่ (ถ้าต้องการเปลี่ยน):</label>
                    <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                </div>
                
                <div class="mb-3">
                    <label for="link_url" class="form-label">ลิงก์ URL:</label>
                    <input type="url" class="form-control" id="link_url" name="link_url" value="<?= htmlspecialchars($slide['link_url']) ?>">
                </div>

                <div class="mb-3">
                    <label for="alt_text" class="form-label">ข้อความสำรอง (Alt Text):</label>
                    <input type="text" class="form-control" id="alt_text" name="alt_text" value="<?= htmlspecialchars($slide['alt_text']) ?>">
                </div>

                <div class="mb-3">
                    <label for="order_no" class="form-label">ลำดับการแสดง:</label>
                    <input type="number" class="form-control" id="order_no" name="order_no" value="<?= htmlspecialchars($slide['order_no']) ?>">
                </div>
                
                <button type="submit" name="update_slide" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
            </form>
        <?php endif; ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>