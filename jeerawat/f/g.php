<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง) โปรแกรมสูตรคูณ</title>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์ (อิคคิวซัง) โปรแกรมสูตรคูณ</h1>
<form method="post" action="">
	กรอกแม่สูตรคูณ <input type="number" min="0" max="100"  name= "a" autofocus required>
    <button type = "submit" name="Submit">OK</button>
</form>

<?php
if(isset($_POST['Submit'])){
	$m = $_POST['a'];
	for($i=1; $i<=12;$i++){
		$x = $m * $i ;
		echo "{$m} x {$i} = {$x}<br>";
	}
}

?>

</body>
</html>