<?php
session_start();
require '../db.php'; // ตรวจสอบให้แน่ใจว่า path ไปยัง db.php ถูกต้อง

$error_message = ''; // เพิ่มตัวแปรสำหรับเก็บข้อความผิดพลาด

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_code = trim($_POST['student_code']); // เพิ่ม trim() เพื่อลบช่องว่าง
    $student_name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $major = trim($_POST['major']);

    // ตรวจสอบข้อมูลเบื้องต้น
    if (empty($student_code) || empty($student_name) || empty($phone) || empty($major)) {
        $error_message = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!preg_match("/^\d{11}$/", $student_code)) {
        $error_message = "รหัสนิสิตต้องเป็นตัวเลข 11 หลัก";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error_message = "เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 หลัก";
    } else {
        // ตรวจสอบว่ารหัสนิสิตมีอยู่แล้วหรือไม่
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE student_code = ?");
        $check_stmt->bind_param("s", $student_code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // ถ้ารหัสนิสิตมีอยู่แล้ว ให้ดึง ID เก่า
            $student_data = $check_result->fetch_assoc();
            $student_db_id = $student_data['id'];
            // อาจจะอัปเดตข้อมูลนิสิตเก่าแทนการ insert ใหม่ ถ้าต้องการ
            // $update_stmt = $conn->prepare("UPDATE students SET name = ?, phone = ?, major = ? WHERE id = ?");
            // $update_stmt->bind_param("sssi", $student_name, $phone, $major, $student_db_id);
            // $update_stmt->execute();
            // $update_stmt->close();
        } else {
            // เตรียมคำสั่ง SQL สำหรับ insert ข้อมูลนิสิตใหม่
            $stmt = $conn->prepare("INSERT INTO students (student_code, name, phone, major) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                $error_message = 'Prepare failed: ' . htmlspecialchars($conn->error);
            } else {
                $stmt->bind_param("ssss", $student_code, $student_name, $phone, $major);
                if ($stmt->execute()) {
                    $student_db_id = $stmt->insert_id; // ดึง Primary Key ที่ถูกสร้างขึ้นมา
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }

        if (empty($error_message) && isset($student_db_id)) {
            // เก็บข้อมูลที่จำเป็นใน Session เพื่อนำไปใช้ในขั้นตอนถัดไป
            $_SESSION['student_db_id'] = $student_db_id; // เก็บ Primary Key ของนิสิต
            $_SESSION['last_student_code'] = $student_code; // อาจเก็บ student_code ไว้แสดงผลหรือตรวจสอบ

            // Redirect ไปยัง step2.php พร้อมส่ง student_id ผ่าน URL
            header("Location: step2.php?student_id=" . urlencode($student_db_id));
            exit; // สำคัญมาก: ต้องเรียก exit; หลัง header()
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
    <title>บันทึกข้อมูลนิสิต - ระบบสำรองที่นั่ง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: linear-gradient(to bottom, #e0f2f7, #ffffff, #e0f2f7); /* โทนฟ้าอ่อน สดใส */
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 550px;
            width: 100%;
            margin: auto;
            animation: fadeIn 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-family: 'Sarabun', sans-serif;
            text-align: center;
            color: #007bff; /* สีน้ำเงินสดใส */
            margin-bottom: 30px;
            font-size: 2.2em;
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 1.05em;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            outline: none;
        }

        .btn-primary {
            background-color: #007bff; /* สีน้ำเงินหลัก */
            border-color: #007bff;
            padding: 12px 25px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .btn-primary:hover {
            background-color: #0056b3; /* สีน้ำเงินเข้มขึ้นเมื่อ hover */
            border-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            font-size: 1em;
            color: #6c757d; /* สีเทา */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #495057;
            text-decoration: underline;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }

        /* Custom styles for select placeholder */
        .form-select option[value=""][disabled] {
            display: none;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>บันทึกข้อมูลนิสิต</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="student_code" class="form-label">รหัสนิสิต (11 หลัก)</label>
                <input type="text" class="form-control" name="student_code" id="student_code" pattern="\d{11}" maxlength="11" required value="<?= htmlspecialchars($_POST['student_code'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" class="form-control" name="name" id="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">เบอร์โทร (10 หลัก)</label>
                <input type="text" class="form-control" name="phone" id="phone" pattern="[0-9]{10}" maxlength="10" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label for="major" class="form-label">สาขา</label>
                <select name="major" id="major" class="form-select" required>
                    <option value="" disabled selected>--- เลือกสาขา ---</option>
                    <option value="Accounting" <?= (($_POST['major'] ?? '') == 'Accounting') ? 'selected' : '' ?>>สาขาบัญชีบัณฑิต</option>
                    <option value="Business Economics" <?= (($_POST['major'] ?? '') == 'Business Economics') ? 'selected' : '' ?>>สาขาเศรษฐศาสตร์ธุรกิจ</option>
                    <option value="Business Computer Science" <?= (($_POST['major'] ?? '') == 'Business Computer Science') ? 'selected' : '' ?>>สาขาวิชาคอมพิวเตอร์ธุรกิจ</option>
                    <option value="Modern Management" <?= (($_POST['major'] ?? '') == 'Modern Management') ? 'selected' : '' ?>>สาขาการจัดการสมัยใหม่</option>
                    <option value="International Business" <?= (($_POST['major'] ?? '') == 'International Business') ? 'selected' : '' ?>>สาขาธุรกิจระหว่างประเทศ</option>
                    <option value="Marketing" <?= (($_POST['major'] ?? '') == 'Marketing') ? 'selected' : '' ?>>สาขาการตลาด</option>
                    <option value="Financial Management" <?= (($_POST['major'] ?? '') == 'Financial Management') ? 'selected' : '' ?>>สาขาการบริหารการเงิน</option>
                    <option value="Digital Business and Information Systems" <?= (($_POST['major'] ?? '') == 'Digital Business and Information Systems') ? 'selected' : '' ?>>สาขาธุรกิจดิจิทัลและระบบสารสนเทศ</option>
                    <option value="Modern Entrepreneurship" <?= (($_POST['major'] ?? '') == 'Modern Entrepreneurship') ? 'selected' : '' ?>>สาขาการเป็นผู้ประกอบการธุรกิจสมัยใหม่</option>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">ถัดไป</button>
            </div>
        </form>
        <a href="../index.php" class="back-link">กลับหน้าแรก</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>