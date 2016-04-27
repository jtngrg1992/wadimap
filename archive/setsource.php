<?php
    session_start();
$_SESSION['source']=$_POST['source'];
echo $_SESSION['source'];
?>