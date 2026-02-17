<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง) โปรแกรมสูตรคูณ</title>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง) โปรแกรมสูตรคูณ</h1>
<form method="post" action="">
	กรอก-รหัสนิสิต <input type="number"   name= "a" autofocus required>
    <button type = "submit" name="Submit">OK</button>
</form>

<?php
if(isset($_POST['Submit'])){
	$id = $_POST['a'];
	$y = substr($id,0,2);
	echo "<img src ='http://202.28.32.211/picture/student/{$y}/{$id}.jpg' width = '500'>";
}

?>

</body>
</html>