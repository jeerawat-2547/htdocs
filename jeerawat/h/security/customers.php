<?php 
    include_once("check_login.php");
    include_once("connectdb.php");
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการลูกค้า - จีรวัฒน์ ศักดิ์วงษ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .nav-link { color: rgba(255,255,255,0.7); transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); }
        .nav-link.active { background: #0d6efd !important; }
        .main-content { padding: 25px; }
        .avatar-circle {
            width: 40px; height: 40px;
            background-color: #e9ecef;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: bold; color: #495057;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3 shadow">
            <div class="text-center my-4">
                <h6 class="mb-0"><?php echo $_SESSION['aname']; ?></h6>
                <small class="text-success fw-bold">Online</small>
            </div>
            <hr class="text-secondary">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="index2.php" class="nav-link mb-2"><i class="bi bi-house-door me-2"></i> หน้าแรก</a></li>
                <li><a href="products.php" class="nav-link mb-2"><i class="bi bi-box-seam me-2"></i> จัดการสินค้า</a></li>
                <li><a href="orders.php" class="nav-link mb-2"><i class="bi bi-cart-check me-2"></i> จัดการออเดอร์</a></li>
                <li><a href="customers.php" class="nav-link active mb-2"><i class="bi bi-people me-2"></i> จัดการลูกค้า</a></li>
                <li class="mt-4"><a href="logout.php" class="nav-link text-danger border border-danger border-opacity-25 text-center rounded">ออกจากระบบ</a></li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">รายชื่อลูกค้า</h1>
                <div class="input-group style='width: 250px;' shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="ค้นหาชื่อลูกค้า...">
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ลูกค้า</th>
                                    <th>เบอร์โทรศัพท์</th>
                                    <th>อีเมล</th>
                                    <th>วันที่สมัคร</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT * FROM customers ORDER BY c_id DESC";
                                    $rs = mysqli_query($conn, $sql);
                                    while ($data = mysqli_fetch_array($rs)) {
                                        // ดึงตัวอักษรแรกของชื่อมาทำ Avatar
                                        $firstChar = mb_substr($data['c_name'], 0, 1, 'UTF-8');
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3"><?php echo $firstChar; ?></div>
                                            <div>
                                                <div class="fw-bold"><?php echo $data['c_name']; ?></div>
                                                <small class="text-muted">ID: #<?php echo $data['c_id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $data['c_phone']; ?></td>
                                    <td><?php echo $data['c_email']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($data['c_created_at'])); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-light border" title="แก้ไขข้อมูล"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" title="ระงับบัญชี"><i class="bi bi-slash-circle"></i></button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <footer class="mt-5 text-center text-muted small">
                &copy; 2026 จีรวัฒน์ ศักดิ์วงษ์ - ระบบหลังบ้านอัจฉริยะ
            </footer>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>