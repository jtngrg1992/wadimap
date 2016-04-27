<?php
session_start();
include ('connection.php');
$sql="select * from users";
if ($res=$conn->query($sql)){
	$result=array();
	while ($result[]=mysqli_fetch_assoc($res)){}
	for ($i=0;$i<count($result);$i++){
		if($_POST['user']==$result[$i]['ID'])
		{
			if($_POST['pwd']==$result[$i]['Password']){
				echo "success";
				$_SESSION['user']=$_POST['user'];
				$_SESSION['password']=$_POST['pwd'];
				if($result[$i]['Lastscrape'])
					echo "mil gyi";
				else{
					$_SESSION['lastscrape']="none";
					print_r($_SESSION);
					foreach($_GET as $k=>$v){
						if($k=='sa')
							header('location:sa.php');

						if($k=='uae')
							header('location:uae.php');
					}



			}
				}

		}

	}
}
else
	echo mysqli_error($conn);
$conn->close();

?>