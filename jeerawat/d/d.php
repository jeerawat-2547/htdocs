<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>ฟอร์มรับข้อมูล - จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง)</title>

<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h3 class="text-center m-0">ฟอร์มรับข้อมูล - จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง)- chatgpt</h3>
        </div>
        <div class="card-body">

<form method="post" action="">
    
    <div class="mb-3">
        <label class="form-label">ชื่อ-นามสกุล *</label>
        <input type="text" name="fullname" class="form-control" required autofocus>
    </div>

    <div class="mb-3">
        <label class="form-label">เบอร์โทร *</label>
        <input type="text" name="phone" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">ส่วนสูง (ซม.) *</label>
        <input type="number" name="height" min="100" max="200" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">ที่อยู่</label>
        <textarea name="address" class="form-control" rows="4"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">วันเดือนปีเกิด</label>
        <input type="date" name="birthday" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">สีที่ชอบ</label>
        <input type="color" name="color" class="form-control form-control-color">
    </div>

    <div class="mb-3">
        <label class="form-label">สาขาวิชา</label>
        <select name="major" class="form-select">
            <option value="การบัญชี">การบัญชี</option>
            <option value="การตลาด">การตลาด</option>
            <option value="การจัดการ">การจัดการ</option>
            <option value="คอมพิวเตอร์ธุรกิจ">คอมพิวเตอร์ธุรกิจ</option>
            <option value="การเงิน">การเงิน</option>
        </select>
    </div>

    <div class="text-center mt-4">
        <button type="submit" name="Submit" class="btn btn-success px-4">สมัครสมาชิก</button>
        <button type="reset" class="btn btn-secondary px-4">ยกเลิก</button>
        <button type="button" class="btn btn-info px-4" onclick="window.location='https://th.mancity.com/';">กดสิ</button>
        <button type="button" class="btn btn-warning px-4" onmouseover="alert('ต๊กกะใจแอ่แห้!!');">ระวัง</button>
        <button type="button" class="btn btn-dark px-4" onclick="window.print();">Print</button>
    </div>

</form>

        </div>
    </div>
</div>

<div class="container mt-4">
<hr>

<?php
if (isset($_POST['Submit'])){
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $height = $_POST['height'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $color = $_POST['color'];
    $major = $_POST['major'];

    echo "<div class='alert alert-primary'><h4>ข้อมูลที่ได้รับ</h4>";
    echo "ชื่อ-สกุล: ".$fullname."<br>";
    echo "เบอร์โทร: ".$phone."<br>";
    echo "ส่วนสูง: ".$height."<br>";
    echo "ที่อยู่: ".$address."<br>";
    echo "วันเดือนปีเกิด: ".$birthday."<br>";
    echo "สีที่ชอบ: <div style='background-color:{$color}; width:300px; height:30px;'></div>".$color."<br>";
    echo "สาขาวิชา: ".$major."<br>";
    echo "</div>";
}
?>
</div>

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
