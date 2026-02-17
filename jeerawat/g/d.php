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

    <th>ประเทศ</th>
    <th>ยอดขาย</th>

</tr>
<?php
include_once("connectdb.php");
$sql = "SELECT `p_country`,SUM(`p_amount`) AS total FROM `popsupermarket`GROUP BY `p_country`; ";
$rs = mysqli_query($conn, $sql);
while ($data = mysqli_fetch_array($rs)){
?>
<tr>
    <td><?php echo $data ['p_country']; ?></td>
    <td align="right"><?php echo number_format($data['total'],0); ?></td>
</tr>
<?php
}
mysqli_close($conn);
?>
</table>
</body>
</html>