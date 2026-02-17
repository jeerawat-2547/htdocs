<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิว)</title>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body {
        font-family: "Segoe UI", Tahoma, sans-serif;
        background: #f4f6f9;
        margin: 20px;
    }

    h1 {
        text-align: center;
        color: #2c3e50;
    }

    .container {
        max-width: 1100px;
        margin: auto;
    }

    .charts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    canvas {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    th, td {
        padding: 12px;
        text-align: center;
    }

    th {
        background: #3498db;
        color: #fff;
    }

    tr:nth-child(even) {
        background: #f2f2f2;
    }

    .right {
        text-align: right;
    }
</style>
</head>

<body>
<h1>รายงานยอดขายแยกตามประเทศ</h1>

<div class="container">

<?php
include_once("connectdb.php");

$sql = "SELECT p_country, SUM(p_amount) AS total 
        FROM popsupermarket 
        GROUP BY p_country";

$rs = mysqli_query($conn, $sql);

$countries = [];
$totals = [];

while ($data = mysqli_fetch_assoc($rs)) {
    $countries[] = $data['p_country'];
    $totals[] = $data['total'];
}
mysqli_close($conn);
?>

<!-- กราฟ -->
<div class="charts">
    <canvas id="barChart"></canvas>
    <canvas id="pieChart"></canvas>
</div>

<!-- ตาราง -->
<table>
    <tr>
        <th>ประเทศ</th>
        <th>ยอดขาย</th>
    </tr>
    <?php foreach ($countries as $i => $country): ?>
    <tr>
        <td><?= $country ?></td>
        <td class="right"><?= number_format($totals[$i], 0) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</div>

<script>
const labels = <?= json_encode($countries); ?>;
const dataValues = <?= json_encode($totals); ?>;

/* Bar Chart */
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'ยอดขาย',
            data: dataValues,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

/* Pie Chart */
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            data: dataValues
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>
