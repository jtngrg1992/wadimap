<?php
session_start();
$cat=$_SESSION['category'];
include('connection.php');
$source=$_POST['source'];
$sql="select * from $source where Category='$cat'";
//echo $sql;
//foreach($_GET as $k=>$v) {
//	if ($k == 'sa')
//		$sql = "select * from wadi_sa where Category='$cat'";
//	if ($k == 'uae')
//		$sql = "select * from wadi_uae where Category='$cat'";
//}
if(!$conn->query($sql))
	echo mysqli_error($conn);
else
	$result=array();
	$res=$conn->query($sql);
	$n=mysqli_num_rows($res);
	while($result[]=mysqli_fetch_assoc($res)){}
	print_r($n.";".$result[0]['ID']);
$conn->close();
?>