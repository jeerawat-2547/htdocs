<?php 
    session_start();
    unset($_SESSION['aid']);
    unset($_SESSION['aname']);

    echo"<script>";
    echo"window.location='index2.php';";
    echo"</script>";
?>