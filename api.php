<?php
	ob_start();
	include('connection.php');
	$response['success']="false";
	$response['data']=0;
	if($_SERVER['REQUEST_METHOD']=="GET"){
		
		$keys=array_keys($_GET);
		foreach($keys as $k){
			if($k=='catlist'){  //searching for paintings using artist name as criteria
				$response['success']="true";
				$response['data']=0;
				$source=$_GET['source'];
				$sql="select distinct Category from $source";
				$res=$conn->query($sql);
				$response['data']=array();
				while($response['data'][]=mysqli_fetch_assoc($res)){}

		 }
		 
		 
		else if($k=='getsouq'){
			$source=$_GET['source'];
			$wadi=$_GET['wadi'];
			if($source=='wadi_uae' || $source=='wadi_uae_uploads')
				$sql="select * from look where Device_ID='$wadi'";
			else
				$sql="select * from lookups_sa where Device_ID='$wadi'";
			$res=$conn->query($sql);
			$response['success']='true';
			$response['data']=array();
			while ($response['data'][]=mysqli_fetch_assoc($res)){}
		}

		else if ($k=='getwadi'){
			$source=$_GET['source'];
			$id=$_GET['id'];
			a:
			$sql="select * from $source where ID=$id";
			$res=$conn->query($sql);
			$response['success']='true';
			$response['data']=array();
			while ($response['data'][]=mysqli_fetch_assoc($res)){}
			//checking if the device has already been mapped
			$device=$response['data'][0]['Device_ID'];
			if($source=='wadi_uae' || $source=='wadi_uae_uploads')
				$target="mappings";
			else
				$target="mappings_sa";
			$sql="select count(*) from $target where Device_ID='$device'";
			$res=$conn->query($sql);
			$res=mysqli_fetch_assoc($res);

			if($res['count(*)']>0){
				$id+=1;
				goto a;
			}
		}

		else if ($k=='getid'){
			$source=$_GET['source'];
			$cat=$_GET['cat'];
			$sql="select ID from $source where Category='$cat' limit 1";
			$res=$conn->query($sql);
			$response['success']='true';
			$response['data']=array();
			while ($response['data'][]=mysqli_fetch_assoc($res)){}
			$sql="select count(*) from $source where Category='$cat'";
			$res=$conn->query($sql);
			$temp=mysqli_num_rows($conn->query("select * from $source where Category='$cat'"));
			$response['count']=$temp;
			
		}

		
		}

		
		
		echo json_encode($response);
		die();
		}//--ends--

		else if($_SERVER['REQUEST_METHOD']=="POST"){
			
			$keys=array_keys($_GET);
			foreach($keys as $k){
				if($k=='login'){
					$data=json_decode(file_get_contents('php://input'));
					$user=$data->user;
					$pwd=$data->pwd;
					$sql="select * from users";
					$res=$conn->query($sql);
					$result=array();
					$response['success']="false";
					$response['data']=0;
					while($result[]=mysqli_fetch_assoc($res)){
					}
					for ($i=0;$i<count($result);$i++){
						if($user==$result[$i]['ID'])
						{
							if($pwd==$result[$i]['Password']){
								if($result[$i]['logged']==1){
									$response['success']=="false";
									$response['data']='User is already logged in, please use different credentials or wait for the user to log out';
								}
								else{
									$response['success']='true';
									$response['data']=="login successful";
								}

						}
						else{
							$response['success']="false";
							$response['data']="Invalid Password";
						}
					}

				}
			}

				else if($k=='map'){
					$source=$_GET['source'];
					if($source=='wadi_uae' || $source=='wadi_uae_uploads')
						$target='mappings';
					else
						$target="mappings_sa";
					$data=json_decode(file_get_contents('php://input'));
					$wadi=$data->wadi;
					$souq=$data->souq;
					$user=$data->user;
					foreach($souq as $item)
						if(isset($item->lookup_id)){
							continue;
						}
						$sql="insert into $target (Device_ID,souq_name,souq_url,souq_price_old,souq_price_new,souq_img,mapped_by)".
							" values("."'".$wadi->Device_ID."'".","."'".$item->souq_name."'".
								","."'".$item->souq_url."'".","."'".$item->souq_oldprice."'".","."'".$item->souq_newprice."'".","."'".$item->souq_img."'".
								",".$user.")";
						if($conn->query($sql)){
							$response['success']="true";
							$response['data']=0;
						}
						else
							$response['success']="false";
							$response['data']=mysqli_error($conn);
				}

				else if($k=="upload"){
					$target=$_POST['source'];
					$file=addslashes($_FILES["csv-file"]["tmp_name"]);
					$searchstring=file_get_contents($file);
					if($target=="wadi_uae_uploads")
						$source="wadi_uae";
					else
						$source="wadi_sa";
					$sql="select * from $source where Device_ID in ($searchstring)";
					print($sql);
					$res=$conn->query($sql);
	 				$result=array();
	 				while ($result[]=mysqli_fetch_assoc($res)){}
	 				$conn->query("TRUNCATE TABLE $target");
	 				print_r($result);
	 				$response['success']="true";
	 				for ($i=0;$i<count($result);$i++) {

						$sql = "insert ignore into $target (Device_ID,Name,Category,Image,Model,Brand,Color,Url,New_Price,Old_Price)
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
						if(!$conn->query($sql)){
							$response['success']="false";
							$response['data']=mysqli_error($conn);
						}
						}
						header('location:index.html');

				}
			}
			echo json_encode($response);
			die();
			
		}
		
		?>
