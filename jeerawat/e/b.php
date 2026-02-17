<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ฟอร์มรับสมัครงาน - [ชื่อบริษัทของคุณ]</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .color-box {
            display: inline-block;
            width: 50px;
            height: 25px;
            border: 1px solid #ccc;
            vertical-align: middle;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h1 class="h3 mb-0">✨ แบบฟอร์มใบสมัครงาน ✨</h1>
                <p class="mb-0">บริษัท จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง) </p>
            </div>
            <div class="card-body">
                <form method="post" action="" class="row g-3">
                    
                    <h2 class="h5 mt-3 mb-3 text-primary">💼 ข้อมูลตำแหน่งงาน</h2>
                    <div class="col-md-12">
                        <label for="position" class="form-label">ตำแหน่งที่ต้องการสมัคร <span class="text-danger">*</span></label>
                        <select id="position" name="position" class="form-select" required>
                            <option value="" selected disabled>--- กรุณาเลือกตำแหน่ง ---</option>
                            <option value="Software Engineer">วิศวกรซอฟต์แวร์</option>
                            <option value="Marketing Manager">ผู้จัดการฝ่ายการตลาด</option>
                            <option value="Accountant">นักบัญชี</option>
                            <option value="HR Specialist">ผู้เชี่ยวชาญฝ่ายบุคคล</option>
                            <option value="Sales Representative">พนักงานขาย</option>
                        </select>
                    </div>

                    <hr class="mt-4 mb-4">

                    <h2 class="h5 mb-3 text-primary">👤 ข้อมูลส่วนตัว</h2>
                    
                    <div class="col-md-2">
                        <label for="prefix" class="form-label">คำนำหน้าชื่อ <span class="text-danger">*</span></label>
                        <select id="prefix" name="prefix" class="form-select" required>
                            <option value="นาย">นาย</option>
                            <option value="นาง">นาง</option>
                            <option value="นางสาว" selected>นางสาว</option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="fullname" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" autofocus required>
                    </div>
                    
                    <div class="col-md-5">
                        <label for="phone" class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" placeholder="เช่น 0812345678" required>
                        <div class="form-text">รูปแบบ: 10 หลัก (0-9)</div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="birthday" class="form-label">วันเดือนปีเกิด</label>
                        <input type="date" class="form-control" id="birthday" name="birthday">
                    </div>

                    <div class="col-md-4">
                        <label for="height" class="form-label">ส่วนสูง (ซม.) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="height" name="height" min="100" max="200" placeholder="150-190" required>
                    </div>

                    <div class="col-md-4">
                        <label for="color" class="form-label">สีที่ชอบ</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff" title="เลือกสี">
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label">ที่อยู่ปัจจุบัน</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                    
                    <hr class="mt-4 mb-4">
                    
                    <h2 class="h5 mb-3 text-primary">🎓 การศึกษา & ประสบการณ์</h2>

                    <div class="col-md-4">
                        <label for="education" class="form-label">ระดับการศึกษาสูงสุด <span class="text-danger">*</span></label>
                        <select id="education" name="education" class="form-select" required>
                            <option value="" selected disabled>--- เลือก ---</option>
                            <option value="มัธยมศึกษาตอนปลาย">มัธยมศึกษาตอนปลาย/ปวช.</option>
                            <option value="อนุปริญญา">อนุปริญญา/ปวส.</option>
                            <option value="ปริญญาตรี">ปริญญาตรี</option>
                            <option value="ปริญญาโท">ปริญญาโท</option>
                            <option value="ปริญญาเอก">ปริญญาเอก</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label for="major" class="form-label">สาขาวิชาเอก / คณะ</label>
                        <input type="text" class="form-control" id="major" name="major" placeholder="เช่น วิทยาการคอมพิวเตอร์">
                    </div>
                    
                    <div class="col-md-12">
                        <label for="skills" class="form-label">ความสามารถพิเศษ / ทักษะ (โปรดระบุ)</label>
                        <textarea class="form-control" id="skills" name="skills" rows="3" placeholder="เช่น ทักษะด้านภาษา, โปรแกรมคอมพิวเตอร์, ใบรับรองวิชาชีพ"></textarea>
                    </div>

                    <div class="col-md-12">
                        <label for="experience" class="form-label">ประสบการณ์ทำงานโดยสรุป (ถ้ามี)</label>
                        <textarea class="form-control" id="experience" name="experience" rows="4" placeholder="โปรดระบุชื่อบริษัท, ตำแหน่ง, และระยะเวลาทำงาน"></textarea>
                    </div>
                    
                    <div class="col-12 mt-4 text-center">
                        <button type="submit" name="Submit" class="btn btn-success btn-lg me-2">
                            <i class="bi bi-send-fill"></i> สมัครสมาชิก
                        </button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg me-2">
                            <i class="bi bi-arrow-counterclockwise"></i> ยกเลิก
                        </button>
                        <button type="button" onClick="window.location='https://th.mancity.com/'; event.preventDefault();" class="btn btn-info text-white me-2">
                            <i class="bi bi-link"></i> กดสิ (Man City)
                        </button>
                        <button type="button" onClick="alert('ต๊กกะใจแอ่แห้!!');" class="btn btn-warning text-dark me-2">
                            <i class="bi bi-exclamation-triangle-fill"></i> ระวัง
                        </button>
                        <button type="button" onClick="window.print();" class="btn btn-outline-primary">
                            <i class="bi bi-printer-fill"></i> พิมพ์
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5">
            <hr>
            <div class="card shadow-sm">f.php
                <div class="card-header bg-light text-dark">
                    <h2 class="h5 mb-0">📝 ผลลัพธ์จากการส่งข้อมูล (PHP Output)</h2>
                </div>
                <div class="card-body">
                    <?php
                    // ตรวจสอบว่ามีการส่งข้อมูล (Submit) หรือไม่
                    if (isset($_POST['Submit'])) {
                        // ดึงข้อมูลจากฟอร์ม
                        $position = isset($_POST['position']) ? htmlspecialchars($_POST['position']) : '-';
                        $prefix = isset($_POST['prefix']) ? htmlspecialchars($_POST['prefix']) : '-';
                        $fullname = isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '-';
                        $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '-';
                        $height = isset($_POST['height']) ? htmlspecialchars($_POST['height']) : '-';
                        $address = isset($_POST['address']) ? nl2br(htmlspecialchars($_POST['address'])) : '-';
                        $birthday = isset($_POST['birthday']) && $_POST['birthday'] != '' ? htmlspecialchars($_POST['birthday']) : '-';
                        $color = isset($_POST['color']) ? htmlspecialchars($_POST['color']) : '#FFFFFF';
                        $education = isset($_POST['education']) ? htmlspecialchars($_POST['education']) : '-';
                        $major = isset($_POST['major']) ? htmlspecialchars($_POST['major']) : '-';
                        $skills = isset($_POST['skills']) ? nl2br(htmlspecialchars($_POST['skills'])) : '-';
                        $experience = isset($_POST['experience']) ? nl2br(htmlspecialchars($_POST['experience'])) : '-';

                        include_once("connectdb.php");

                        $sql = "INSERT INTO for_application(a_id, a_position, a_prefix, a_fullname, a_phone, a_birthday, a_height, a_color, a_address, a_education, a_major, a_skills, a_experience) 
                        VALUES (NULL,'{$position}','{$prefix}','{$fullname}','{$phone}','{$height}','{$address}','{$birthday}' ,'{$color}','{$education}','{$major}','{$skills}','{$experience}');";
                        mysqli_query($conn, $sql) or die ("insert ไม่ได้") ;
                
                        echo "<script>";
                        echo "alert('บันทึกข้อมูลสำเร็จ');";
                        echo "</script>";

                        
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>