<?php
// เปิดการแสดงผลข้อผิดพลาดทั้งหมดสำหรับการพัฒนา
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เริ่มต้น Session และตรวจสอบสิทธิ์การเข้าถึง
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// รวมไฟล์เชื่อมต่อฐานข้อมูล
require_once '../db.php';

// ตรวจสอบว่ามีการส่งค่า id และ action 'delete' มาหรือไม่
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id_to_delete = $_GET['id'];

    // 1. ดึงข้อมูลสไลด์ก่อนลบเพื่อหา path ของรูปภาพ
    $sql_select = "SELECT image_path FROM slides WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $id_to_delete);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select->num_rows > 0) {
        $slide_data = $result_select->fetch_assoc();
        $image_path = "../" . $slide_data['image_path'];
        $stmt_select->close();

        // 2. ลบข้อมูลจากฐานข้อมูล
        $sql_delete = "DELETE FROM slides WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_to_delete);
        
        if ($stmt_delete->execute()) {
            // 3. ลบไฟล์รูปภาพที่เกี่ยวข้อง
            // ตรวจสอบว่าไฟล์มีอยู่จริงและไม่ใช่ไฟล์ default เพื่อป้องกันการลบไฟล์ที่ไม่ได้อัปโหลด
            if (file_exists($image_path) && strpos($image_path, 'uploads/default.jpg') === false) {
                unlink($image_path);
            }
            // Redirect กลับไปหน้าจัดการสไลด์พร้อมข้อความสำเร็จ
            $_SESSION['message'] = 'ลบสไลด์เรียบร้อยแล้ว';
            $_SESSION['message_type'] = 'success';
        } else {
            // Redirect กลับไปหน้าจัดการสไลด์พร้อมข้อความผิดพลาด
            $_SESSION['message'] = 'เกิดข้อผิดพลาดในการลบสไลด์: ' . $stmt_delete->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt_delete->close();
    } else {
        // Redirect กลับไปหน้าจัดการสไลด์พร้อมข้อความไม่พบข้อมูล
        $_SESSION['message'] = 'ไม่พบสไลด์ที่ต้องการลบ';
        $_SESSION['message_type'] = 'danger';
    }
} else {
    // Redirect กลับไปหน้าจัดการสไลด์หากไม่มี id หรือ action ที่ถูกต้อง
    $_SESSION['message'] = 'คำขอไม่ถูกต้อง';
    $_SESSION['message_type'] = 'danger';
}

// Redirect กลับไปหน้า manage_slides.php ทุกครั้งหลังจากการประมวลผลเสร็จสิ้น
header('Location: manage_slides.php');
exit;
?>