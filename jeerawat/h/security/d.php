<?php 
    session_start();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>จีรวัฒน์ ศักดิ์วงษ์(อิคคิวซัง)</title>
</head>

<body>
<h1>จีรวัฒน์ ศักดิ์วงษ์(อิคคิวซัง)</h1>  
<?php 
    echo @$_SESSION['name'] . "<br>";
    echo @$_SESSION['nickname']. "<br>";
    echo @$_SESSION['p1']. "<br>";
    echo @$_SESSION['p2']. "<br>";
?>   
</body>
</html>