<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ผลการสมัครงาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .color-box {
            display: inline-block;
            width: 50px;
            height: 25px;
            border: 1px solid #ccc;
            vertical-align: middle;
            margin-right: 10px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center">
                <h1 class="h3 mb-0"><i class="bi bi-check2-circle"></i> ส่งใบสมัครงานสำเร็จ!</h1>
                <p class="mb-0">ข้อมูลที่คุณกรอกมีดังนี้:</p>
            </div>
            <div class="card-body">
                <?php
                // ตรวจสอบว่ามีการส่งข้อมูล (Submit) หรือไม่
                if (isset($_POST['Submit'])) {
                    // 1. ดึงและทำความสะอาดข้อมูล (ใช้ htmlspecialchars เพื่อป้องกัน XSS)
                    $position = isset($_POST['position']) ? htmlspecialchars($_POST['position']) : 'ไม่ได้ระบุ';
                    $prefix = isset($_POST['prefix']) ? htmlspecialchars($_POST['prefix']) : '';
                    $fullname = isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : 'ไม่ได้ระบุ';
                    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : 'ไม่ได้ระบุ';
                    $height = isset($_POST['height']) ? htmlspecialchars($_POST['height']) : 'ไม่ได้ระบุ';
                    $address = isset($_POST['address']) ? nl2br(htmlspecialchars($_POST['address'])) : 'ไม่ได้ระบุ';
                    $birthday = isset($_POST['birthday']) && $_POST['birthday'] != '' ? htmlspecialchars($_POST['birthday']) : 'ไม่ได้ระบุ';
                    $color = isset($_POST['color']) ? htmlspecialchars($_POST['color']) : '#FFFFFF';
                    $education = isset($_POST['education']) ? htmlspecialchars($_POST['education']) : 'ไม่ได้ระบุ';
                    $major = isset($_POST['major']) ? htmlspecialchars($_POST['major']) : 'ไม่ได้ระบุ';
                    $skills = isset($_POST['skills']) ? nl2br(htmlspecialchars($_POST['skills'])) : 'ไม่ได้ระบุ';
                    $experience = isset($_POST['experience']) ? nl2br(htmlspecialchars($_POST['experience'])) : 'ไม่ได้ระบุ';

                    // 2. แสดงผลข้อมูลในรูปแบบตารางหรือรายการที่สวยงาม
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered table-striped">';
                    
                    echo '<thead class="table-primary">';
                    echo '<tr><th colspan="2" class="text-center h5">ข้อมูลใบสมัคร</th></tr>';
                    echo '</thead>';
                    
                    echo '<tbody>';
                    
                    // ส่วนข้อมูลตำแหน่ง
                    echo '<tr><td colspan="2" class="bg-light"><strong>💼 ข้อมูลตำแหน่งงาน</strong></td></tr>';
                    echo '<tr><td style="width: 30%;">ตำแหน่งที่สมัคร</td><td>' . $position . '</td></tr>';
                    
                    // ส่วนข้อมูลส่วนตัว
                    echo '<tr><td colspan="2" class="bg-light"><strong>👤 ข้อมูลส่วนตัว</strong></td></tr>';
                    echo '<tr><td>ชื่อ-สกุล</td><td>' . $prefix . ' ' . $fullname . '</td></tr>';
                    echo '<tr><td>วันเดือนปีเกิด</td><td>' . $birthday . '</td></tr>';
                    echo '<tr><td>เบอร์โทรศัพท์</td><td>' . $phone . '</td></tr>';
                    echo '<tr><td>ส่วนสูง</td><td>' . $height . ' ซม.</td></tr>';
                    echo '<tr><td>สีที่ชอบ</td><td><span class="color-box" style="background-color:' . $color . ';" title="' . $color . '"></span> <code>' . $color . '</code></td></tr>';
                    echo '<tr><td>ที่อยู่</td><td>' . $address . '</td></tr>';
                    
                    // ส่วนข้อมูลการศึกษา/ประสบการณ์
                    echo '<tr><td colspan="2" class="bg-light"><strong>🎓 การศึกษา & ประสบการณ์</strong></td></tr>';
                    echo '<tr><td>ระดับการศึกษา</td><td>' . $education . '</td></tr>';
                    echo '<tr><td>สาขาวิชาเอก</td><td>' . $major . '</td></tr>';
                    echo '<tr><td>ความสามารถพิเศษ / ทักษะ</td><td>' . $skills . '</td></tr>';
                    echo '<tr><td>ประสบการณ์ทำงาน</td><td>' . $experience . '</td></tr>';

                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>'; // close table-responsive
                    
                    echo '<div class="alert alert-info mt-4" role="alert">';
                    echo 'ขอบคุณที่ให้ความสนใจ บริษัทจะติดต่อกลับไปตามข้อมูลที่ท่านได้ให้ไว้';
                    echo '</div>';

                } else {
                    // กรณีเข้าถึงไฟล์ f.php โดยตรงโดยไม่ได้ส่งข้อมูล
                    echo '<div class="alert alert-danger" role="alert">';
                    echo '<h4 class="alert-heading">🚫 ข้อผิดพลาด!</h4>';
                    echo '<p>คุณเข้าถึงหน้านี้โดยไม่ได้ส่งข้อมูลจากแบบฟอร์มใบสมัคร กรุณาย้อนกลับไปกรอกข้อมูลให้ครบถ้วน</p>';
                    echo '<a href="[ชื่อไฟล์ฟอร์มของคุณ].html" class="btn btn-danger mt-2">ย้อนกลับไปที่ฟอร์ม</a>';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="card-footer text-muted text-center">
                © <?= date("Y"); ?> [ชื่อบริษัทของคุณ]
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>