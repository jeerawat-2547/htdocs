<?php 
    session_start();
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบเข้าสู่ระบบ - จีรวัฒน์ ศักดิ์วงษ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 100px; }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="card shadow-sm">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">เข้าสู่ระบบ</h3>
            <p class="text-center text-muted">จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง)</p>
            
            <form method="post" action=""> 
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="auser" class="form-control" placeholder="ชื่อผู้ใช้งาน" autofocus required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="apwd" class="form-control" placeholder="รหัสผ่าน" required>
                </div>
                <div class="d-grid">
                    <button type="submit" name="Submit" class="btn btn-primary">LOGIN</button>
                </div>
            </form>

            <?php
                if(isset($_POST['Submit'])){
                    include_once("connectdb.php");
                    
                    $user = $_POST['auser'];
                    $pwd  = $_POST['apwd'];

                    // 1. ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
                    $stmt = $conn->prepare("SELECT a_id, a_name, a_password FROM admin WHERE a_username = ? LIMIT 1");
                    $stmt->bind_param("s", $user);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows == 1) {
                        $data = $result->fetch_assoc();
                        
                        // 2. ตรวจสอบรหัสผ่านที่เข้ารหัส (Password Hashing)
                        if (password_verify($pwd, $data['a_password'])) {
                            $_SESSION['aid'] = $data['a_id'];
                            $_SESSION['aname'] = $data['a_name'];
                            
                            echo "<div class='alert alert-success mt-3'>เข้าสู่ระบบสำเร็จ! กำลังเปลี่ยนหน้า...</div>";
                            echo "<script>setTimeout(function(){ window.location='index2.php'; }, 1500);</script>";
                        } else {
                            echo "<div class='alert alert-danger mt-3'>Username หรือ Password ไม่ถูกต้อง</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger mt-3'>Username หรือ Password ไม่ถูกต้อง</div>";
                    }
                    $stmt->close();
                }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>