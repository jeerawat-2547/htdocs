<?php
session_start();
require '../db.php'; // ตรวจสอบให้แน่ใจว่า path ไปยัง db.php ถูกต้อง

// ดึง student_id จาก URL parameter
$student_id_from_url = $_GET['student_id'] ?? null;

// ตรวจสอบว่ามี student_id ส่งมาหรือไม่
if (!$student_id_from_url) {
    die("ไม่พบข้อมูลนิสิต กรุณาทำรายการใหม่จากขั้นตอนแรก");
}

// ดึงข้อมูลนิสิต รวมถึงชื่อและสาขา (major) ด้วย
$stmt_check_student = $conn->prepare("SELECT id, name, major FROM students WHERE id = ?");
$stmt_check_student->bind_param("i", $student_id_from_url);
$stmt_check_student->execute();
$result_check_student = $stmt_check_student->get_result();

if ($result_check_student->num_rows === 0) {
    die("ไม่พบนิสิตในระบบ กรุณาตรวจสอบ student_id หรือลงทะเบียนนิสิตใหม่");
}
$student_details = $result_check_student->fetch_assoc(); // ดึงข้อมูลทั้งหมดมาเป็น array
$student_id_pk = $student_details['id'];
$student_name = $student_details['name']; // เก็บชื่อนิสิต
$student_major = $student_details['major']; // เก็บสาขาของนิสิต
$stmt_check_student->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจาก POST
    // ไม่จำเป็นต้องใช้ $_POST['student_id'] เพราะเราใช้ $student_id_pk ที่ได้จาก URL parameter แล้ว
    $course_ids_array = $_POST['course_id'] ?? []; // รับเป็น Array ของ course_id
    $reason = $_POST['reason'] ?? '';
    $other_reason = $_POST['other_reason'] ?? '';
    $gpa = $_POST['gpa'] ?? '';
    $department_code = $_POST['department_code'] ?? '';
    $faculty = $_POST['faculty'] ?? '';
    $major = $_POST['major'] ?? '';
    $degree_status = $_POST['degree_status'] ?? '';

    // --- ตรวจสอบข้อมูลเบื้องต้น ---
    if (empty($course_ids_array)) {
        die("กรุณาเลือกรายวิชาอย่างน้อยหนึ่งวิชา");
    }

    // --- ตรวจสอบว่าแต่ละรายวิชามีจริงในฐานข้อมูล และสร้างอาร์เรย์ของ course IDs ที่ถูกต้อง ---
    $valid_course_ids = [];
    foreach ($course_ids_array as $c_id) {
        $c_id = (int)$c_id; // แปลงเป็น integer เพื่อความปลอดภัย
        if ($c_id > 0) { // ตรวจสอบว่าเป็นค่าที่ถูกต้อง
            $check = $conn->prepare("SELECT id FROM courses WHERE id = ?");
            $check->bind_param("i", $c_id);
            $check->execute();
            $result = $check->get_result();
            if ($result->num_rows > 0) {
                $valid_course_ids[] = $c_id; // เพิ่มเฉพาะ course ID ที่มีอยู่จริง
            }
            $check->close();
        }
    }

    if (empty($valid_course_ids)) {
        die("ไม่พบข้อมูลรายวิชาที่เลือก หรือรายวิชาไม่ถูกต้อง");
    }

    // --- เก็บข้อมูลทั้งหมดลง session เพื่อใช้งานต่อใน step3.php ---
    $_SESSION['reservation_data'] = [
        'student_id' => $student_id_pk, // ใช้ Primary Key ของ student
        'course_ids' => $valid_course_ids, // เก็บเป็น Array ของ course IDs ที่ตรวจสอบแล้ว
        'reason' => $reason,
        'other_reason' => $other_reason,
        'gpa' => $gpa,
        'department_code' => $department_code,
        'faculty' => $faculty,
        'major' => $major,
        'degree_status' => $degree_status,
    ];

    // Redirect ไปยัง step3.php
    // ส่งแค่ student_id_pk ไปก็พอ เพราะข้อมูล course_ids อยู่ใน Session แล้ว
    header("Location: step3.php?student_id=" . urlencode($student_id_pk));
    exit;
}

// ดึง student_code จากฐานข้อมูลเพื่อนำไปแสดงผลในช่องรหัสนิสิต (readonly)
$student_code_display = '';
$stmt_get_student_code = $conn->prepare("SELECT student_code FROM students WHERE id = ?");
$stmt_get_student_code->bind_param("i", $student_id_pk);
$stmt_get_student_code->execute();
$result_get_student_code = $stmt_get_student_code->get_result();
if ($result_get_student_code->num_rows > 0) {
    $student_code_display = $result_get_student_code->fetch_assoc()['student_code'];
}
$stmt_get_student_code->close();

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>เลือกรายวิชาที่ต้องการสำรองที่นั่ง</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #004080;
            margin-bottom: 30px;
            font-weight: 700;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 64, 128, 0.15);
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #004080;
            margin-top: 15px;
        }
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px 12px;
            font-size: 1em;
            border-radius: 8px;
            border: 1.5px solid #90caf9;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #1976d2;
            outline: none;
            box-shadow: 0 0 6px #64b5f6;
        }
        #otherReasonDiv {
            margin-top: 8px;
            display: none;
        }
        button[type="submit"] {
            margin-top: 30px;
            width: 100%;
            padding: 14px 0;
            font-size: 1.2em;
            font-weight: 700;
            background-color: #1976d2;
            border: none;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #125ea9;
        }
        button[type="submit"]:disabled {
            background-color: #a0a0a0;
            cursor: not-allowed;
        }
        .course-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }
        .course-row select {
            flex-grow: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #90caf9;
            font-size: 1em;
        }
        .course-row button {
            font-weight: bold;
            font-size: 1.3em;
            border-radius: 8px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            line-height: 1;
            display: flex; /* ใช้ flexbox จัดกึ่งกลาง */
            justify-content: center; /* จัดกึ่งกลางแนวนอน */
            align-items: center; /* จัดกึ่งกลางแนวตั้ง */
        }
        .addCourseBtn {
            background:#fff;
            color:#1d3557;
            border: 2px solid #0a2a56ff;
        }
        .removeCourseBtn {
            background:#ef5350;
            color:#fff;
            border:none;
        }
    </style>
</head>
<body>

<h2>เลือกรายวิชาที่ต้องการสำรองที่นั่ง</h2>

<form method="post" id="reserveForm">

    <label for="student_id">รหัสนิสิต</label>
    <input type="text" name="student_id_display" id="student_id_display" value="<?php echo htmlspecialchars($student_code_display); ?>" readonly>
    <input type="hidden" name="student_id" id="student_id" value="<?php echo htmlspecialchars($student_id_pk); ?>">


    <label for="faculty">คณะ</label>
    <select name="faculty" id="faculty" required>
        <option value="">-- เลือกคณะ --</option>
        <option value="คณะการบัญชีและการจัดการ">คณะการบัญชีและการจัดการ</option>
    </select>

     <label for="major">สาขา</label>
<input type="text" name="major_display" id="major_display" value="<?= htmlspecialchars($student_major) ?>" readonly>
<input type="hidden" name="major" id="major" value="<?= htmlspecialchars($student_major) ?>">
    </select>

    <label for="gpa">เกรดเฉลี่ย (GPA)</label>
    <input type="number" name="gpa" id="gpa" step="0.01" min="0" max="4" required>

    <label for="degree_status">นิสิตระดับปริญญาตรี</label>
    <select name="degree_status" id="degree_status" required>
        <option value="Regular System">ระบบปกติ</option>
        <option value="Special Program">ระบบพิเศษ</option>
        <option value="Special Program Transfer">เทียบเข้าระบบพิเศษ</option>
        <option value="Continuing Education">ต่อเนื่อง</option>
    </select>

    <label for="department_code">สังกัดห้องเรียน</label>
    <input type="text" name="department_code" id="department_code" pattern="[A-Za-z]{2}\d{3}" required placeholder="เช่น AC532">

    <label>รายวิชาที่ต้องการสำรอง:</label>
    <div id="courses-container">
        <div class="course-row">
            <select name="course_id[]" required>
                <option value="">-- เลือกรายวิชา --</option>
                <?php
                // ดึงข้อมูลรายวิชาจากฐานข้อมูลเพื่อแสดงใน dropdown
                $result = $conn->query("SELECT id, code, name, group_name FROM courses ORDER BY code ASC");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" .
                             htmlspecialchars($row['code']) . " - " .
                             htmlspecialchars($row['name']) . " (กลุ่ม " .
                             htmlspecialchars($row['group_name']) . ")</option>";
                    }
                }
                ?>
            </select>
            <button type="button" class="addCourseBtn">+</button>
            <button type="button" class="removeCourseBtn" style="display:none;">−</button>
        </div>
    </div>

    <label for="reason">เหตุผลที่ขอสำรองที่นั่ง</label>
    <select name="reason" id="reason" required onchange="toggleOtherReason()">
        <option value="">-- กรุณาระบุเหตุผล --</option>
        <option value="ลงแก้ F">ลงแก้ F</option>
        <option value="เคย W">เคย W</option>
        <option value="เก็บรายวิชาเรียนไม่ตามแผน">เก็บรายวิชาเรียนไม่ตามแผน</option>
        <option value="ลงทะเบียนไม่ทัน">ลงทะเบียนไม่ทัน</option>
        <option value="ติดเวลาเรียนซ้ำ">ติดเวลาเรียนซ้ำ</option>
        <option value="เหตุผลอื่น ๆ">เหตุผลอื่น ๆ</option>
    </select>

    <div id="otherReasonDiv">
        <label for="other_reason">กรุณาระบุเหตุผลอื่น ๆ:</label>
        <input type="text" name="other_reason" id="other_reason">
    </div>

    <button type="submit" id="submitBtn">ถัดไป</button>
</form>

<script>
    // ฟังก์ชันสำหรับสลับการแสดงผลช่องเหตุผลอื่น ๆ
    function toggleOtherReason() {
        var reason = document.getElementById("reason").value;
        var otherReasonDiv = document.getElementById("otherReasonDiv");
        var otherReasonInput = document.getElementById("other_reason");

        if (reason === "เหตุผลอื่น ๆ") {
            otherReasonDiv.style.display = "block";
            otherReasonInput.setAttribute('required', 'required'); // ทำให้ required
        } else {
            otherReasonDiv.style.display = "none";
            otherReasonInput.value = ""; // ล้างค่าเมื่อซ่อน
            otherReasonInput.removeAttribute('required'); // ลบ required ออก
        }
        checkFormCompletion(); // ตรวจสอบความสมบูรณ์ของฟอร์ม
    }

    // ฟังก์ชันสำหรับตรวจสอบความสมบูรณ์ของฟอร์มเพื่อเปิด/ปิดปุ่ม submit
    function checkFormCompletion() {
        var student_id_field = document.getElementById("student_id").value;
        var faculty = document.getElementById("faculty").value;
        var major = document.getElementById("major").value;
        var gpa = document.getElementById("gpa").value;
        var degree_status = document.getElementById("degree_status").value;
        var department_code = document.getElementById("department_code").value;
        var reason = document.getElementById("reason").value;
        var other_reason = document.getElementById("other_reason").value;

        // ตรวจสอบว่ามี course_id ถูกเลือกอย่างน้อย 1 รายการและทุกรายการที่เพิ่มมาต้องถูกเลือก
        var courseSelects = document.querySelectorAll('select[name="course_id[]"]');
        var allCoursesSelected = true;
        if (courseSelects.length === 0) {
            allCoursesSelected = false; // ต้องมีอย่างน้อย 1 แถว
        } else {
            courseSelects.forEach(function(select) {
                if (select.value === "") {
                    allCoursesSelected = false;
                }
            });
        }

        var submitBtn = document.getElementById("submitBtn");

        // เงื่อนไขการเปิดใช้งานปุ่ม submit
        if (
            student_id_field !== "" &&
            faculty !== "" &&
            major !== "" &&
            gpa !== "" &&
            degree_status !== "" &&
            department_code !== "" &&
            allCoursesSelected && // ตรวจสอบว่ามีการเลือกวิชาครบถ้วน
            reason !== "" &&
            (reason !== "เหตุผลอื่น ๆ" || other_reason.trim() !== "") // ตรวจสอบเหตุผลอื่น ๆ
        ) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }

    // เมื่อโหลดหน้าเว็บและเมื่อมีการเปลี่ยนแปลงในฟอร์ม
    window.onload = function() {
        toggleOtherReason(); // ตั้งค่าเริ่มต้นสำหรับเหตุผลอื่น ๆ
        checkFormCompletion(); // ตรวจสอบสถานะฟอร์มเมื่อโหลดหน้า
    };

    // เพิ่ม Event Listener ให้กับทุก input และ select เพื่อเรียก checkFormCompletion
    document.querySelectorAll("input, select").forEach(function(el) {
        el.addEventListener("input", checkFormCompletion);
        el.addEventListener("change", function() {
            checkFormCompletion();
            // ตรวจสอบ toggleOtherReason เฉพาะเมื่อมีการเปลี่ยนใน select 'reason'
            if (this.id === "reason") toggleOtherReason();
        });
    });

    // JavaScript สำหรับการเพิ่ม/ลบแถวรายวิชา
    const coursesContainer = document.getElementById('courses-container');

    coursesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('addCourseBtn')) {
            const firstRow = coursesContainer.querySelector('.course-row');
            if (!firstRow) return;
            // โคลนแถวแรกทั้งหมด
            const newRow = firstRow.cloneNode(true);
            newRow.querySelector('select').value = ''; // ล้างค่าที่เลือกไว้ในแถวใหม่
            const removeBtn = newRow.querySelector('.removeCourseBtn');
            if (removeBtn) {
                removeBtn.style.display = 'flex'; // แสดงปุ่มลบสำหรับแถวใหม่
            }
            coursesContainer.appendChild(newRow);
            updateRemoveButtons(); // อัปเดตสถานะปุ่มลบ
            checkFormCompletion(); // ตรวจสอบสถานะฟอร์มหลังเพิ่มแถว

            // เพิ่ม event listener ให้กับ select ในแถวใหม่ด้วย
            newRow.querySelector('select[name="course_id[]"]').addEventListener("change", checkFormCompletion);

        } else if (e.target.classList.contains('removeCourseBtn')) {
            // ต้องมีอย่างน้อย 1 แถวเสมอ (ห้ามลบแถวสุดท้าย)
            if (coursesContainer.querySelectorAll('.course-row').length > 1) {
                e.target.parentElement.remove();
                updateRemoveButtons(); // อัปเดตสถานะปุ่มลบ
                checkFormCompletion(); // ตรวจสอบสถานะฟอร์มหลังลบแถว
            }
        }
    });

    // ฟังก์ชันอัปเดตการแสดงผลปุ่มลบ
    function updateRemoveButtons() {
        const removeButtons = coursesContainer.querySelectorAll('.removeCourseBtn');
        const rows = coursesContainer.querySelectorAll('.course-row');
        // แสดงปุ่มลบเมื่อมีแถวมากกว่า 1 แถว
        removeButtons.forEach(btn => btn.style.display = rows.length > 1 ? 'flex' : 'none');
    }

    // เรียกครั้งแรกเพื่ออัปเดตปุ่มเมื่อโหลดหน้า
    updateRemoveButtons();

    // เพิ่ม event listener ให้กับ select ที่มีอยู่แล้วเมื่อโหลดหน้า (สำหรับแถวแรก)
    document.querySelector('select[name="course_id[]"]').addEventListener("change", checkFormCompletion);

</script>
</body>
</html>