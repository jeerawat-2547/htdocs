<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ฟอร์มรับข้อมูล - จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง)</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
    /* เพิ่ม CSS เล็กน้อยเพื่อจัดกึ่งกลางเนื้อหา (ถ้าต้องการ) */
    .container {
        max-width: 700px; /* จำกัดความกว้างของฟอร์ม */
    }
    .color-box {
        display: inline-block;
        width: 100px;
        height: 20px;
        border: 1px solid #ccc;
        vertical-align: middle;
        margin-right: 5px;
    }
</style>
</head>

<body>
<div class="container my-5">
    <h1 class="text-center mb-4">ฟอร์มรับข้อมูล - จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง) - Gemini</h1>

    <form method="post" action="" class="p-4 border rounded shadow-sm" novalidate>
        
        <div class="mb-3">
            <label for="fullname" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
            <input type="text" name="fullname" id="fullname" class="form-control" autofocus required>
        </div>
        
        <div class="mb-3">
            <label for="phone" class="form-label">เบอร์โทร <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="height" class="form-label">ส่วนสูง (ซม.) <span class="text-danger">*</span></label>
            <input type="number" name="height" id="height" class="form-control" min="100" max="200" required>
        </div>
        
        <div class="mb-3">
            <label for="address" class="form-label">ที่อยู่</label>
            <textarea name="address" id="address" class="form-control" rows="4"></textarea>
        </div>	

        <div class="mb-3">
            <label for="birthday" class="form-label">วันเดือนปีเกิด</label>
            <input type="date" name="birthday" id="birthday" class="form-control">
        </div>
        
        <div class="mb-3">
            <label for="color" class="form-label">สีที่ชอบ</label>
            <input type="color" name="color" id="color" class="form-control form-control-color" value="#563d7c" title="Choose your color">
        </div>
        
        <div class="mb-3">
            <label for="major" class="form-label">สาขาวิชา</label>
            <select name="major" id="major" class="form-select">
                <option value="การบัญชี">การบัญชี</option>
                <option value="การตลาด">การตลาด</option>
                <option value="การจัดการ">การจัดการ</option>
                <option value="คอมพิวเตอร์ธุรกิจ">คอมพิวเตอร์ธุรกิจ</option>
                <option value="การเงิน">การเงิน</option>
            </select>
        </div>
        
        <div class="d-grid gap-2 d-md-block mt-4">
            <button type="submit" name="Submit" class="btn btn-success">
                <i class="bi bi-person-plus-fill"></i> สมัครสมาชิก
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="bi bi-x-circle-fill"></i> ยกเลิก
            </button>
            <button type="button" class="btn btn-info text-white" onClick="window.location='https://th.mancity.com/';">
                กดสิ
            </button>
            <button type="button" class="btn btn-warning" onmouseover="alert('ต๊กกะใจแอ่แห้!!');">
                ระวัง
            </button>
            <button type="button" class="btn btn-light border" onClick="window.print();">
                <i class="bi bi-printer-fill"></i> print
            </button>
        </div>

    </form>
    
    <hr class="my-5">

    <?php
    if (isset($_POST['Submit'])){
        $fullname = $_POST['fullname'];
        $phone = $_POST['phone'];
        $height = $_POST['height'];
        $address = $_POST['address'];
        $birthday = $_POST['birthday'];
        $color = $_POST['color'];
        $major = $_POST['major'];

        echo "<div class='alert alert-primary' role='alert'>";
        echo "<h4 class='alert-heading'>🎉 ผลลัพธ์จากการส่งฟอร์ม</h4>";
        echo "<ul class='list-unstyled'>";
        echo "<li><strong>ชื่อ-สกุล:</strong> {$fullname}</li>";
        echo "<li><strong>เบอร์โทร:</strong> {$phone}</li>";
        echo "<li><strong>ส่วนสูง:</strong> {$height} ซม.</li>";
        echo "<li><strong>ที่อยู่:</strong> {$address}</li>";
        echo "<li><strong>วันเดือนปีเกิด:</strong> {$birthday}</li>";
        echo "<li><strong>สีที่ชอบ:</strong> <span class='color-box' style='background-color:{$color};'></span> {$color}</li>";
        echo "<li><strong>สาขาวิชา:</strong> {$major}</li>";
        echo "</ul>";
        echo "</div>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</body>
</html>