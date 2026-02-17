<?php 
    include_once("check_login.php");
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - จีรวัฒน์ ศักดิ์วงษ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .nav-link { color: rgba(255,255,255,0.7); transition: 0.3s; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        .nav-link.active { color: #fff; background: #0d6efd; }
        .main-content { padding: 25px; }
        .card-stat { border: none; border-radius: 15px; transition: transform 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3 shadow">
            <div class="text-center my-4">
                <div class="bg-light rounded-circle d-inline-block p-3 mb-2">
                    <i class="bi bi-person-badge text-dark fs-1"></i>
                </div>
                <h6 class="mb-0"><?php echo $_SESSION['aname']; ?></h6>
                <small class="text-success fw-bold"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online</small>
            </div>
            
            <hr class="text-secondary">
            
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="index2.php" class="nav-link active mb-2">
                        <i class="bi bi-house-door me-2"></i> หน้าแรก
                    </a>
                </li>
                <li>
                    <a href="products.php" class="nav-link mb-2">
                        <i class="bi bi-box-seam me-2"></i> จัดการสินค้า
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="nav-link mb-2">
                        <i class="bi bi-cart-check me-2"></i> จัดการออเดอร์
                    </a>
                </li>
                <li>
                    <a href="customers.php" class="nav-link mb-2">
                        <i class="bi bi-people me-2"></i> จัดการลูกค้า
                    </a>
                </li>
                <li class="mt-4">
                    <a href="logout.php" class="nav-link text-danger border border-danger border-opacity-25 text-center rounded">
                        <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                    </a>
                </li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">แผงควบคุม (Dashboard)</h1>
                <div class="text-muted">จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง)</div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-xl-4 mb-4">
                    <div class="card card-stat shadow-sm bg-primary text-white h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h6 class="card-title text-uppercase opacity-75">สินค้าทั้งหมด</h6>
                                <h2 class="mb-0 fw-bold">128</h2>
                            </div>
                            <i class="bi bi-box fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 mb-4">
                    <div class="card card-stat shadow-sm bg-success text-white h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h6 class="card-title text-uppercase opacity-75">รายการสั่งซื้อ</h6>
                                <h2 class="mb-0 fw-bold">45</h2>
                            </div>
                            <i class="bi bi-receipt fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4 mb-4">
                    <div class="card card-stat shadow-sm bg-warning text-dark h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h6 class="card-title text-uppercase opacity-75">จำนวนลูกค้า</h6>
                                <h2 class="mb-0 fw-bold">89</h2>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-2 border-0 overflow-hidden">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">ข่าวประกาศจากระบบ</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">ยินดีต้อนรับเข้าสู่ระบบจัดการข้อมูล คุณ <strong><?php echo $_SESSION['aname']; ?></strong> ขณะนี้ระบบทำงานปกติ</p>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>