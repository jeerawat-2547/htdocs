<?php

// เปิดการแสดงผลข้อผิดพลาดทั้งหมดสำหรับการพัฒนา
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เริ่มต้น Session เพื่อใช้งานตัวแปร Session
session_start();

// รวมไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db.php';

// ดึง student_id ที่จองล่าสุดจาก session เพื่อนำไปแสดงคิวของผู้ใช้
$last_booked_student_id = $_SESSION['last_booked_student_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบสำรองที่นั่ง - คณะการบัญชีและการจัดการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Base Styles */
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f5f5f7;
            color: #1d1d1f;
            line-height: 1.6;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px; /* ปรับขนาดสูงสุดของเนื้อหาหลัก */
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header & Hero Section */
        .hero {
            background: linear-gradient(to bottom, #0289ffff, #ffffff, #0389ffff);
            padding: 80px 20px 20px;
            box-shadow: inset 0 -2px 10px rgba(0,0,0,0.05);
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 900;
            margin: 0 auto 10px;
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.1rem);
            color: #5f5f63;
            margin: 0;
        }
        
        /* Module Card Styles - For the centered cards */
        .module-card {
            max-width: 900px;
            margin: 10px auto; /* จัดกึ่งกลางด้วย margin */
            padding: 35px;
            background: #ffffffff;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Specific card for the welcome message - REDUCED MAX-WIDTH */
        .welcome-card {
            max-width: 900px; /* ลดขนาดสูงสุดของ card ต้อนรับ */
            padding: 25px;  /* ลดขนาด padding */
        }
        
        /* Specific card for queue status to have smaller max-width */
        .queue-card {
            max-width: 600px; /* ลดขนาดสูงสุดของ card คิว */
        }
        
        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
        }

        .module-card h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            margin-bottom: 24px;
        }
        
        .welcome-card h2 {
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
        }

        .queue-card h2 {
            font-size: clamp(1.3rem, 2.5vw, 1.8rem);
        }

        /* Buttons - Reduced size */
        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-block;
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #0071e3;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #005bb5;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #e5e5ea;
            color: #1d1d1f;
        }

        .btn-secondary:hover {
            background-color: #d0d0d5;
            transform: translateY(-2px);
        }

        /* Queue Status Section */
        .queue-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .queue-box {
            background-color: #f9fafc;
            padding: 28px;
            border-radius: 14px;
            border: 1px solid #d2d2d7;
            min-width: 200px;
            flex: 1;
            max-width: 100px;
        }

        .queue-box .label {
            font-size: 0.95rem;
            color: #060606ff;
            margin-bottom: 8px;
        }

        .queue-box .number {
            font-size: 2.6rem;
            font-weight: bold;
            display: block;
        }

        #current-queue .number { color: #fbfb0cff; }
        #user-queue .number { color: #f71114ff; }

        /* สไลด์ - ปรับปรุงใหม่เพื่อให้เต็มความกว้างจอ */
        .full-width-carousel {
            width: 100%; /* ทำให้กว้างเต็มหน้าจอ */
            margin: 10px 0; /* ลบการจัดกึ่งกลางออก */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .full-width-carousel .carousel-item img {
            width: 100%; /* ใช้ความกว้างเต็มตามกรอบของ carousel */
            height: 300px; /* ปรับความสูงตามต้องการ */
            object-fit: contain;
            object-position: center;
        }

        /* Footer */
        footer {
            background-color: #f5f5f7;
            color: #1515c3ff;
            font-size: 0.85rem;
            padding: 40px 20px;
            margin-top: 10px;
            border-top: 1px solid #e5e5ea;
        }

        /* Media queries for smaller screens */
        @media (max-width: 768px) {
            .queue-card, .welcome-card {
                max-width: 100%;
            }
            .full-width-carousel .carousel-item img {
                height: 200px; /* ลดความสูงของสไลด์สำหรับมือถือ */
            }
        }
    </style>
</head>
<body>
    <header class="hero">
        <div class="container">
            <h1>ระบบสำรองที่นั่ง</h1>
            <p>คณะการบัญชีและการจัดการ</p>
        </div>
    </header>

    <div id="carouselExampleControls" class="carousel slide full-width-carousel" data-bs-ride="carousel">   
        <div class="carousel-inner">
            <?php
            // ดึงข้อมูลสไลด์จากฐานข้อมูล
            $sql_slides = "SELECT * FROM slides ORDER BY order_no ASC";
            $result_slides = $conn->query($sql_slides);
            
            $is_first_item = true;
            if ($result_slides && $result_slides->num_rows > 0) {
                while($row = $result_slides->fetch_assoc()) {
                    $active_class = $is_first_item ? 'active' : '';
                    ?>
                    <div class="carousel-item <?= $active_class ?>">
                        <?php if (!empty($row['link_url'])): ?>
                            <a href="<?= htmlspecialchars($row['link_url']) ?>" target="_blank">
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($row['alt_text']) ?>">
                        <?php if (!empty($row['link_url'])): ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php
                    $is_first_item = false;
                }
            } else {
                // กรณีไม่มีสไลด์ในฐานข้อมูล
                echo '<div class="carousel-item active"><p>ยังไม่มีสไลด์ที่ตั้งค่าไว้</p></div>';
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <main class="container">
        <section class="module-card welcome-card">
            <h2>ยินดีต้อนรับเข้าสู่ระบบสำรองที่นั่งเรียน</h2>
            <div class="buttons">
                <a href="student/step1.php" class="btn btn-primary">สำหรับนิสิต</a>
                <a href="admin/login.php" class="btn btn-secondary">สำหรับเจ้าหน้าที่วิชาการ</a>
            </div>
        </section>

        <section class="module-card queue-card">
            <h2>สถานะคิวปัจจุบัน</h2>
            <div class="queue-grid">
                <div class="queue-box" id="current-queue">
                    <div class="label">คิวที่</div>
                    <div class="number">
                        7
                    </div>
                </div>
                <div class="queue-box" id="user-queue">
                    <div class="label">คิวของคุณ:</div>
                    <div class="number">
                        9
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="container">
            &copy; 2025 คณะการบัญชีและการจัดการ. สงวนลิขสิทธิ์.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const lastBookedStudentId = <?= json_encode($last_booked_student_id) ?>;

        function updateQueueStatus() {
            const timestamp = new Date().getTime();
            const fetchUrl = `student/get_queue_status.php?_=${timestamp}`;

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const currentQueueElement = document.querySelector('#current-queue .number');
                    const userQueueElement = document.querySelector('#user-queue .number');

                    if (currentQueueElement) {
                        currentQueueElement.textContent = data.current ? data.current : 'ไม่มีคิวที่รอดำเนินการ';
                    }

                    if (userQueueElement) {
                        if (lastBookedStudentId && data.user) {
                            userQueueElement.textContent = data.user;
                        } else if (lastBookedStudentId && !data.user) {
                            userQueueElement.textContent = 'คิวของคุณเสร็จสิ้นแล้ว / ไม่พบ';
                        } else {
                            userQueueElement.textContent = 'ยังไม่ได้จองคิว';
                        }
                    }
                })
                .catch(error => {
                    console.error('Queue fetch error:', error);
                    if (document.querySelector('#current-queue .number')) {
                        document.querySelector('#current-queue .number').textContent = 'เกิดข้อผิดพลาด';
                    }
                    if (document.querySelector('#user-queue .number')) {
                        document.querySelector('#user-queue .number').textContent = 'เกิดข้อผิดพลาด';
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', updateQueueStatus);
        setInterval(updateQueueStatus, 3000);
    </script>
</body>
</html>