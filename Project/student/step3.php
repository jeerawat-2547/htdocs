<?php
ini_set('display_errors', 1); // แสดงข้อผิดพลาดทั้งหมด (สำหรับการพัฒนา)
error_reporting(E_ALL);

session_start(); // เริ่มต้นการใช้งาน Session

// ตรวจสอบว่า db.php มีอยู่จริงและสามารถเรียกใช้งานได้
// ในการใช้งานจริง ควรตรวจสอบ Path ให้ถูกต้อง
require '../db.php';

// --- ตรวจสอบข้อมูลการจองใน Session ---
// หากไม่มีข้อมูลใน Session และไม่ได้มาจากสถานะ 'processed' ให้กลับไปขั้นตอนแรก
if (!isset($_SESSION['reservation_data']) && !isset($_GET['status'])) {
    die("ข้อมูลการจองไม่สมบูรณ์ กรุณาเริ่มทำรายการใหม่จากขั้นตอนแรก");
}

$data = $_SESSION['reservation_data'] ?? []; // ใช้ array ว่างถ้าไม่มี (สำหรับแสดงผลหลังประมวลผล)

// ดึง student_id จาก session หรือจาก URL (หลังจาก redirect มา)
$student_id_from_session = $data['student_id'] ?? ($_GET['student_id'] ?? null);
$course_ids_from_session = $data['course_ids'] ?? []; // นี่คือ Array ของ course IDs

// ดึงข้อมูลเพิ่มเติมจาก Session สำหรับการแสดงผล
$reason = $data['reason'] ?? '';
$other_reason = $data['other_reason'] ?? '';
$gpa = $data['gpa'] ?? '';
$department_code = $data['department_code'] ?? '';
$faculty = $data['faculty'] ?? '';
$major = $data['major'] ?? '';
$degree_status = $data['degree_status'] ?? '';

// ตรวจสอบความสมบูรณ์ของข้อมูลที่จำเป็น เฉพาะกรณีที่เป็นการโหลดหน้าครั้งแรก (ยังไม่ได้ประมวลผล)
if (!isset($_GET['status']) && (!$student_id_from_session || !is_array($course_ids_from_session) || empty($course_ids_from_session))) {
    die("ข้อมูลนิสิตหรือรายวิชาไม่ถูกต้อง/ไม่ครบถ้วนใน Session");
}

$booking_results = []; // เก็บผลลัพธ์การจองแต่ละวิชา

// --- ประมวลผลคำขอแบบ POST สำหรับการยืนยันการจอง ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($course_ids_from_session)) {
        die("ไม่พบรายวิชาที่ต้องการจอง กรุณาเลือกวิชาใหม่"); // ไม่ควรเกิดขึ้นถ้าตรวจสอบข้างต้นผ่าน
    }

    // --- NEW LOGIC: Determine the single next queue number for this entire booking session ---
    $next_queue_for_session = 0;
    try {
        $stmt_max_overall_q = $conn->prepare("SELECT MAX(queue_number) AS max_q FROM bookings");
        $stmt_max_overall_q->execute();
        $result_max_overall_q = $stmt_max_overall_q->get_result();
        $row_max_overall_q = $result_max_overall_q->fetch_assoc();

        if ($row_max_overall_q && isset($row_max_overall_q['max_q']) && is_numeric($row_max_overall_q['max_q'])) {
            $next_queue_for_session = (int) $row_max_overall_q['max_q'] + 1;
        } else {
            // If no bookings exist yet, start from 1
            $next_queue_for_session = 1;
        }
        $stmt_max_overall_q->close();
    } catch (Exception $e) {
        error_log("Database error in step3.php (getting max overall queue): " . $e->getMessage());
        die("เกิดข้อผิดพลาดในการดึงหมายเลขคิว กรุณาลองใหม่");
    }
    // --- END NEW LOGIC ---

    foreach ($course_ids_from_session as $course_id) {
        // 1. ตรวจสอบว่านิสิตคนนี้ได้จองวิชานี้ไปแล้วหรือยัง
        $stmt_check_dup = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE student_id = ? AND course_id = ?");
        $stmt_check_dup->bind_param("ii", $student_id_from_session, $course_id);
        $stmt_check_dup->execute();
        $result_dup = $stmt_check_dup->get_result();
        $total_bookings = $result_dup->fetch_assoc()['total'] ?? 0;
        $stmt_check_dup->close();

        if ($total_bookings > 0) {
            // หากจองวิชานี้ไปแล้ว ให้ข้ามไปวิชาถัดไป
            $booking_results[] = [
                'course_id' => $course_id,
                'status' => 'skipped',
                'message' => 'จองวิชานี้ไปแล้ว',
                'queue_number' => null // กำหนดให้เป็น null ชัดเจนสำหรับรายการที่ข้าม
            ];
            continue;
        }

        // 3. บันทึกการจองลงตาราง bookings (Using the determined $next_queue_for_session)
        $stmt_booking = $conn->prepare("INSERT INTO bookings (student_id, course_id, queue_number) VALUES (?, ?, ?)");
        $stmt_booking->bind_param("iii", $student_id_from_session, $course_id, $next_queue_for_session); // Use the single queue number

        if ($stmt_booking->execute()) {
            $bookings_id = $stmt_booking->insert_id; // ดึง booking_id ที่ถูกสร้างขึ้น
            $stmt_booking->close();

            // 4. บันทึกข้อมูลเพิ่มเติมลงตาราง reservations
            $stmt_reservation = $conn->prepare("INSERT INTO reservations (bookings_id, reason, other_reason, gpa, department_code, faculty, major, degree_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            // ตรวจสอบค่า 'other_reason' อีกครั้ง หาก 'reason' ไม่ใช่ 'เหตุผลอื่น ๆ' ควรเป็นค่าว่าง
            $final_other_reason = ($reason === "เหตุผลอื่น ๆ") ? $other_reason : '';

            $stmt_reservation->bind_param("isssssss", $bookings_id, $reason, $final_other_reason, $gpa, $department_code, $faculty, $major, $degree_status);

            if ($stmt_reservation->execute()) {
                $stmt_reservation->close();
                $booking_results[] = [
                    'course_id' => $course_id,
                    'status' => 'success',
                    'message' => 'สำรองที่นั่งสำเร็จ',
                    'queue_number' => $next_queue_for_session // <-- ตรงนี้มั่นใจได้ว่ามีค่า
                ];
            } else {
                // หากบันทึกข้อมูล reservation ไม่สำเร็จ
                $booking_results[] = [
                    'course_id' => $course_id,
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูลเพิ่มเติม (Reservation): ' . $stmt_reservation->error,
                    'queue_number' => null
                ];
                $stmt_reservation->close();
                // อาจจะต้องลบข้อมูลที่เพิ่ง insert ลง bookings ไปแล้วออกด้วย หากต้องการให้ข้อมูลสอดคล้องกัน
                // $conn->query("DELETE FROM bookings WHERE id = $bookings_id");
            }
        } else {
            // หากบันทึกข้อมูล booking ไม่สำเร็จ
            $booking_results[] = [
                'course_id' => $course_id,
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกการจอง (Booking): ' . $stmt_booking->error,
                'queue_number' => null
            ];
            $stmt_booking->close();
        }
    }

    // หลังจากประมวลผลทุกวิชาแล้ว ค่อยล้าง Session ของ reservation_data
    unset($_SESSION['reservation_data']);

    // เก็บผลลัพธ์การจองไว้ใน Session ชั่วคราวเพื่อแสดงในหน้านี้หลังจาก redirect
    $_SESSION['booking_results_display'] = $booking_results;

    // เก็บ student_id และคิวสุดท้ายที่ได้ (หากมีการจองสำเร็จ) เพื่อใช้ใน get_queue_status.php
    $any_successful_booking = false;
    foreach($booking_results as $res) {
        if ($res['status'] === 'success') {
            $any_successful_booking = true;
            break;
        }
    }
    if ($any_successful_booking) {
        $_SESSION['user_last_queue'] = $next_queue_for_session;
    } else {
        $_SESSION['user_last_queue'] = null; // No successful booking, so no queue number to display
    }
    $_SESSION['last_booked_student_id'] = $student_id_from_session;


    // Redirect กลับมาที่หน้านี้อีกครั้ง เพื่อป้องกันการส่งฟอร์มซ้ำ (Post/Redirect/Get pattern)
    // ส่ง student_id และ status=processed ไปด้วย
    header("Location: step3.php?student_id=" . urlencode($student_id_from_session) . "&status=processed");
    exit;
}

// --- ตรรกะการแสดงผลสำหรับ GET request (โหลดหน้าครั้งแรก หรือหลังจาก redirect มา) ---
$display_results = [];
if (isset($_GET['status']) && $_GET['status'] == 'processed' && isset($_SESSION['booking_results_display'])) {
    $display_results = $_SESSION['booking_results_display'];
    // หลังจากแสดงผลแล้ว ค่อยล้าง Session เพื่อไม่ให้แสดงซ้ำเมื่อรีเฟรชหน้า
    unset($_SESSION['booking_results_display']);
}

// ดึงข้อมูลนิสิตเพื่อแสดงผล (จาก student_id_from_session ที่มาจาก URL หรือ Session)
$student_info = null;
if ($student_id_from_session) {
    $stmt_student_info = $conn->prepare("SELECT student_code, name, phone, major FROM students WHERE id = ?");
    $stmt_student_info->bind_param("i", $student_id_from_session);
    $stmt_student_info->execute();
    $result_student_info = $stmt_student_info->get_result();
    if ($result_student_info->num_rows > 0) {
        $student_info = $result_student_info->fetch_assoc();
    }
    $stmt_student_info->close();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันการสำรองที่นั่ง</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e6f2ff; /* Light blue background */
            margin: 0;
            padding: 20px;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 50, 100, 0.1);
            text-align: center;
            margin-top: 30px;
        }
        h2 {
            color: #004080;
            margin-bottom: 25px;
            font-weight: 700;
            font-size: 2.5em;
        }
        .student-info, .reservation-details {
            background-color: #f8fcff; /* Very light blue for sections */
            border: 1px solid #cce0ff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: left;
        }
        .student-info p, .reservation-details p, .reservation-details ul {
            margin: 8px 0;
            font-size: 1.1em;
        }
        .student-info strong, .reservation-details strong {
            color: #004080;
        }
        .confirm-button-area {
            margin-top: 30px;
        }
        button[type="submit"] {
            padding: 15px 40px;
            font-size: 1.3em;
            font-weight: 700;
            background-color: #28a745; /* Green for confirm */
            border: none;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        button[type="submit"]:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .result-message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
        }
        .result-message.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .result-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .result-message.skipped {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeeba;
        }
        .booking-summary {
            margin-top: 20px;
            text-align: left;
        }
        .booking-summary h3 {
            color: #004080;
            margin-bottom: 15px;
        }
        .booking-summary ul {
            list-style-type: none;
            padding: 0;
        }
        .booking-summary li {
            background-color: #f0f7ff;
            border: 1px solid #d0e7ff;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            word-break: break-word; /* เพิ่มเพื่อให้ข้อความยาวๆ ไม่เลยขอบ */
        }
        .booking-summary li .status-icon {
            font-size: 1.2em;
            margin-left: 10px;
        }
        .status-icon.success { color: #28a745; }
        .status-icon.error { color: #dc3545; }
        .status-icon.skipped { color: #ffc107; }
    </style>
</head>
<body>

    <div class="container">
        <?php if (!isset($_GET['status']) || $_GET['status'] !== 'processed') : // แสดงฟอร์มกดสำรองที่นั่งครั้งแรก ?>
            <h2>ยืนยันข้อมูลการสำรองที่นั่ง</h2>

            <div class="student-info">
                <h3>ข้อมูลนิสิต</h3>
                <?php if ($student_info) : ?>
                    <p><strong>รหัสนิสิต:</strong> <?php echo htmlspecialchars($student_info['student_code'] ?? ''); ?></p>
                    <p><strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($student_info['name'] ?? ''); ?></p>
                    <p><strong>เบอร์โทร:</strong> <?php echo htmlspecialchars($student_info['phone'] ?? ''); ?></p>
                    <p><strong>สาขา:</strong> <?php echo htmlspecialchars($student_info['major'] ?? ''); ?></p>
                <?php else : ?>
                    <p>ไม่พบข้อมูลนิสิต กรุณากลับไปขั้นตอนแรก</p>
                <?php endif; ?>
            </div>

            <div class="reservation-details">
                <h3>รายละเอียดการสำรองที่นั่ง</h3>
                <p><strong>เกรดเฉลี่ย (GPA):</strong> <?php echo htmlspecialchars($gpa ?? ''); ?></p>
                <p><strong>สังกัดห้องเรียน:</strong> <?php echo htmlspecialchars($department_code ?? ''); ?></p>
                <p><strong>คณะ:</strong> <?php echo htmlspecialchars($faculty ?? ''); ?></p>
                <p><strong>สาขา:</strong> <?php echo htmlspecialchars($major ?? ''); ?></p>
                <p><strong>ระดับปริญญาตรี:</strong> <?php echo htmlspecialchars($degree_status ?? ''); ?></p>
                <p><strong>เหตุผลที่ขอสำรอง:</strong>
                    <?php echo htmlspecialchars($reason ?? ''); ?>
                    <?php if ($reason === "เหตุผลอื่น ๆ" && !empty($other_reason)) : ?>
                        (<?php echo htmlspecialchars($other_reason ?? ''); ?>)
                    <?php endif; ?>
                </p>
                <p><strong>รายวิชาที่ต้องการสำรอง:</strong></p>
                <ul>
                    <?php
                    // ดึงชื่อวิชาจาก course_id
                    foreach ($course_ids_from_session as $course_id) {
                        $stmt_course = $conn->prepare("SELECT code, name, group_name FROM courses WHERE id = ?");
                        $stmt_course->bind_param("i", $course_id);
                        $stmt_course->execute();
                        $result_course = $stmt_course->get_result();
                        $course_info = $result_course->fetch_assoc();
                        $stmt_course->close();

                        if ($course_info) {
                            echo "<li>" . htmlspecialchars($course_info['code'] ?? '') . " - " . htmlspecialchars($course_info['name'] ?? '') . " (กลุ่ม " . htmlspecialchars($course_info['group_name'] ?? '') . ")</li>";
                        } else {
                            echo "<li>(ไม่พบข้อมูลวิชา ID: " . htmlspecialchars($course_id ?? '') . ")</li>";
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="confirm-button-area">
                <form method="post">
                    <button type="submit">ยืนยันการสำรองที่นั่ง</button>
                </form>
            </div>
        <?php else : // แสดงผลลัพธ์หลังจากการประมวลผล ?>
            <h2>ผลการสำรองที่นั่ง</h2>
            <?php if (!empty($display_results)) : ?>
                <div class="booking-summary">
                    <h3>สรุปผลการดำเนินการ</h3>
                    <ul>
                        <?php foreach ($display_results as $res) : ?>
                            <li>
                                <span>
                                    <?php
                                    // ดึงข้อมูลวิชาเพื่อแสดงผล
                                    $stmt_course_res = $conn->prepare("SELECT code, name FROM courses WHERE id = ?");
                                    $stmt_course_res->bind_param("i", $res['course_id']);
                                    $stmt_course_res->execute();
                                    $result_course_res = $stmt_course_res->get_result();
                                    $course_name_info = $result_course_res->fetch_assoc();
                                    $stmt_course_res->close();

                                    if ($course_name_info) {
                                        echo htmlspecialchars($course_name_info['code'] ?? '') . " - " . htmlspecialchars($course_name_info['name'] ?? '');
                                    } else {
                                        echo "วิชา ID: " . htmlspecialchars($res['course_id'] ?? '');
                                    }
                                    ?>
                                </span>
                                <span>
                                    <?php if ($res['status'] === 'success') : ?>
                                        <span class="status-icon success">&#10004;</span> สำเร็จ (คิวที่: <?php echo htmlspecialchars($res['queue_number'] ?? ''); ?>)
                                    <?php elseif ($res['status'] === 'skipped') : ?>
                                        <span class="status-icon skipped">&#9888;</span> <?php echo htmlspecialchars($res['message'] ?? ''); ?>
                                    <?php else : ?>
                                        <span class="status-icon error">&#10006;</span> <?php echo htmlspecialchars($res['message'] ?? ''); ?>
                                    <?php endif; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else : ?>
                <div class="result-message error">
                    <p>ไม่พบข้อมูลการประมวลผลการจอง หรือเกิดข้อผิดพลาด</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <a href="../index.php" class="back-link">กลับหน้าหลัก</a>
    </div>

</body>
</html>