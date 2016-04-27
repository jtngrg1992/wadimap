<?php
session_start();
$wadi=$_POST['wadi'];
$source=$_POST['source'];
if($source=='wadi_uae' || $source=='wadi_uae_uploads')
	$sql="select * from look where Device_ID='$wadi'";
else
	$sql="select * from lookups_sa where Device_ID='$wadi'";
include('connection.php');
$res=$conn->query($sql);
if($res){
	$result=array();
	while($result[]=mysqli_fetch_assoc($res)){}
	print(json_encode($result));
}
else
	echo "error encountered";
?>