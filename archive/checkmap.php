<?php
session_start();
include('connection.php');
$source=$_POST['source'];
if($source=='wadi_uae' || $source=='wadi_uae_uploads')
	$sql="select * from mappings where Device_ID="."'".$_POST['item']['Device_ID']."'";
else
	$sql="select * from mappings_sa where Device_ID="."'".$_POST['item']['Device_ID']."'";
//echo $sql;
if(!$result=$conn->query($sql))
{
	echo "error";
}
else
{
$res=array();
while($res[]=mysqli_fetch_assoc($result)){
}
$res=json_encode($res);
echo $res;
}
$conn->close();
?>