<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์fwerwafffwqggerwวงษ์ (อิคคิว)</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .chart-container { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }
    .chart-box { width: 45%; min-width: 300px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
</style>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิว)</h1>


<hr>

<?php
include_once("connectdb.php");
// ดึงข้อมูลยอดขายแยกตามเดือน
$sql = "SELECT MONTH(p_date) AS Month, SUM(p_amount) AS Total_Sales 
        FROM popsupermarket 
        GROUP BY MONTH(p_date) 
        ORDER BY Month";
$rs = mysqli_query($conn, $sql);

$m_list = [];
$s_list = [];

// เก็บข้อมูลลง Array
while ($data = mysqli_fetch_array($rs)) {
    $m_list[] = "เดือน " . $data['Month']; // ชื่อเดือน
    $s_list[] = $data['Total_Sales'];      // ยอดขาย
}
?>

<table border="1">
    <tr bgcolor="#eeeeee">
        <th>Month</th>
        <th>Total Sales</th>
    </tr>
    <?php foreach ($m_list as $key => $month) { ?>
    <tr>
        <td><?php echo $month; ?></td>
        <td align="right"><?php echo number_format($s_list[$key], 0); ?></td>
    </tr>
    <?php } ?>
</table>

<div class="chart-container">
    <div class="chart-box">
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box">
        <canvas id="doughnutChart"></canvas>
    </div>
</div>

<script>
// 3. เตรียมข้อมูลจาก PHP สำหรับ JavaScript
const labels = <?php echo json_encode($m_list); ?>;
const dataValues = <?php echo json_encode($s_list); ?>;

// ตั้งค่าสีพื้นฐาน
const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'];

// วาด Bar Chart
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'ยอดขายรายเดือน',
            data: dataValues,
            backgroundColor: colors
        }]
    }
});

// วาด Doughnut Chart
new Chart(document.getElementById('doughnutChart'), {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            data: dataValues,
            backgroundColor: colors
        }]
    }
});
</script>

<?php mysqli_close($conn); ?>
</body>
</html>