<?php
session_start();
include('connection.php');
$conn->query('delete from test');
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$file = addslashes($_FILES["csv-file"]["tmp_name"]);
	$content=array();
	$content=explode(',',file_get_contents( $file));
	$searchstring=file_get_contents($file);

	$sql="select * from wadi_uae where Device_ID in ($searchstring)";
	 $res=$conn->query($sql);
	 $result=array();
	 while ($result[]=mysqli_fetch_assoc($res)){}

	for ($i=0;$i<count($result);$i++) {

		$sql = "insert ignore into wadi_uae_uploads (Device_ID,Name,Category,Image,Model,Brand,Color,Url,New_Price,Old_Price)
 		values('" . mysqli_real_escape_string($conn, $result[$i]['Device_ID']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Name']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Category']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Image']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Model']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Brand']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Color']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Url']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['New_Price']) .
				"','" . mysqli_real_escape_string($conn, $result[$i]['Old_Price']) . "')";

		if (!$conn->query($sql)){
			echo mysqli_error($conn);
			break;}

	}
header('location:uae.php');
}