<?php 
    include_once("check_login.php");
    include_once("connectdb.php");
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการออเดอร์ - จีรวัฒน์ ศักดิ์วงษ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: Sarabun, sans-serif; background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; }
        .nav-link { color: rgba(255,255,255,0.7); transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); }
        .nav-link.active { background: #0d6efd !important; }
        .main-content { padding: 25px; }
        .table-card { border-radius: 15px; border: none; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-3 shadow">
            <div class="text-center my-4">
                <h6 class="mb-0"><?php echo $_SESSION['aname']; ?></h6>
                <small class="text-success fw-bold"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online</small>
            </div>
            <hr class="text-secondary">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="index2.php" class="nav-link mb-2"><i class="bi bi-house-door me-2"></i> หน้าแรก</a></li>
                <li><a href="products.php" class="nav-link mb-2"><i class="bi bi-box-seam me-2"></i> จัดการสินค้า</a></li>
                <li><a href="orders.php" class="nav-link active mb-2"><i class="bi bi-cart-check me-2"></i> จัดการออเดอร์</a></li>
                <li><a href="customers.php" class="nav-link mb-2"><i class="bi bi-people me-2"></i> จัดการลูกค้า</a></li>
                <li class="mt-4"><a href="logout.php" class="nav-link text-danger border border-danger border-opacity-25 text-center rounded">ออกจากระบบ</a></li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">รายการสั่งซื้อทั้งหมด</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2"><i class="bi bi-file-earmark-excel"></i> Export</button>
                    <button type="button" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i> พิมพ์รายงาน</button>
                </div>
            </div>

            <div class="card table-card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">เลขที่ออเดอร์</th>
                                    <th>วันที่สั่งซื้อ</th>
                                    <th>ลูกค้า</th>
                                    <th>ยอดรวม</th>
                                    <th>สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // ตัวอย่าง SQL (ปรับตามโครงสร้าง DB ของคุณ)
                                    $sql = "SELECT * FROM orders ORDER BY o_id DESC"; 
                                    $rs = mysqli_query($conn, $sql);
                                    while ($data = mysqli_fetch_array($rs)) {
                                        // สุ่มสีสถานะเพื่อเป็นตัวอย่าง
                                        $status_class = ($data['o_status'] == 'paid') ? 'bg-success' : 'bg-warning text-dark';
                                        $status_text = ($data['o_status'] == 'paid') ? 'จ่ายแล้ว' : 'รอชำระเงิน';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#<?php echo $data['o_id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($data['o_date'])); ?></td>
                                    <td><?php echo $data['c_name']; ?></td>
                                    <td class="text-primary fw-bold"><?php echo number_format($data['o_total'], 2); ?> ฿</td>
                                    <td><span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                    <td class="text-center">
                                        <a href="order_detail.php?id=<?php echo $data['o_id']; ?>" class="btn btn-info btn-sm text-white">
                                            <i class="bi bi-eye"></i> รายละเอียด
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-center text-muted mt-4 small">ระบบจัดการออเดอร์โดย จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง)</p>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>