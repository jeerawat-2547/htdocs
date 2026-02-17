<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิว)</title>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิว)</h1>
<table border="1">
<tr>
	<th>Order ID</th>
    <th>ชื่อสินค้า</th>
    <th>ประเภทสินค้า</th>
    <th>วันที่</th>
    <th>ประเทศ</th>
    <th>จำนวนเงิน</th>
    <th>รูปภาพ</th>
</tr>
<?php
include_once("connectdb.php");
$sql = "SELECT * FROM `popsupermarket`";
$rs = mysqli_query($conn, $sql);
while ($data = mysqli_fetch_array($rs)){
?>
<tr>
	<td><?php echo $data ['p_order_id']; ?></td>
    <td><?php echo $data ['p_product_name']; ?></td>
    <td><?php echo $data ['p_category']; ?></td>
    <td><?php echo $data ['p_date']; ?></td>
    <td><?php echo $data ['p_country']; ?></td>
    <td align="right"><?php echo number_format($data['p_amount'],0); ?></td>
    <td><img src ="images/<?php echo $data['p_product_name'];?>.jpg" width="55"></td>
</tr>
<?php
}
mysqli_close($conn);
?>
</table>
</body>
</html>