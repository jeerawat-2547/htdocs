<!doctype html>
<html>
<head> 
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์ 66010914003</title>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์ 66010914003</h1>

<form method="post" action="">
    ชื่อภาค <input type="text" name="rname" autofocus required>
    <button type ="submit" name="Submit"> บันทึก </button>
</form><br><br>

<?php 
if(isset($_POST['Submit'])){
    include_once("connectdb.php");
    $rname = $_POST['rname'];
    $sql2 = "INSERT INTO `regions` (`r_id`, `r_name`) VALUES (NULL, '{$rname}')";
    mysqli_query($conn,$sql2) or die ("เพิ่มข้อมูลไม่ได้");
}
?>

<table border="1">
    <tr>
        <th>รหัสภาค</th>
        <th>ชื่อภาค</th>
        <th>ลบ</th>
    </tr>
<?php
include_once("connectdb.php");
$sql = "SELECT * FROM `regions`";
$rs = mysqli_query($conn,$sql);
while($data = mysqli_fetch_array($rs)){
?>
    <tr>
        <td><?php echo $data['r_id'];?></td>
        <td><?php echo $data['r_name'];?></td>
        <td width="80" align="center"><a href = "delete_regions.php?id=<?php echo $data['r_id'];?>" onClick="return confirm('ยืนยันการลบ');" ><img src =images/tung.jpg width ="20"></a></td>   
    </tr>
<?php } ?>
</table>
</body>
</html>

<?php
mysqli_close($conn);
?>