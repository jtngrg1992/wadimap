<?php
    session_start();
    $category=$_POST['category'];
    $_SESSION['category']= $category;
    print($_SESSION['category']);
    ?>