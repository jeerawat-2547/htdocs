<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิว)</title>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิว)</h1>


<hr>

<table border="1">
<tr>
	<th>Month</th>
    <th>Total_Sales</th>

</tr>
<?php
include_once("connectdb.php");
@$kw = $_POST['a'];
$sql = "SELECT 
    MONTH(p_date) AS Month, 
    SUM(p_amount) AS Total_Sales
FROM popsupermarket
GROUP BY MONTH(p_date)
ORDER BY Month;";
$rs = mysqli_query($conn, $sql);
$total =  0;
while ($data = mysqli_fetch_array($rs)){
	@$total += $data['p_amount'];
?>
<tr>

    <td><?php echo $data ['Month']; ?></td>
    <td align="right"><?php echo number_format($data['Total_Sales'],0); ?></td>

</tr>
<?php
}
?>




<?php
mysqli_close($conn);
?>
</table>
</body>
</html>