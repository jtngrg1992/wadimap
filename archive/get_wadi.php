<?php
session_start();
include('connection.php');
$id=$_POST['item'];
$source=$_POST['source'];

$sql="select * from $source where ID=$id";

if(!$res=$conn->query($sql))
	echo mysqli_error($conn);
else{
	$result=mysqli_fetch_assoc($res);

	//checking if device is already mapped or not
	$sql="select Device_ID from mappings where Device_ID="."'".$result['Device_ID']."'";
	if(!$res=$conn->query($sql))
		echo mysqli_error($conn);
	else
		{
			$result2=mysqli_fetch_assoc($res);
			if(count($result2)==1)
				print_r("found");
			else
				echo(json_encode($result));
		}

	 }
$conn->close();
