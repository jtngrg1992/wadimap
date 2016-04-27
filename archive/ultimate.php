<?php
	$name="Huawei Mate S Dual SIM- 64GB, 4G LTE, Gold";
	$name=strtolower($name);
	$tmp = exec("python ultimate.py '$name'");
	echo "done";
	$handle=fopen('ultimate.csv','r+');
	while($row=fgetcsv($handle,0,";")){
		echo $row[1];
	}
	?>
	