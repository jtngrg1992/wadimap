<?php
$servername = "localhost";
$username = "root";
$password = "jlabs@123";

// Create connection
$conn = mysqli_connect($servername, $username, $password, 'zadmin_wadi');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}