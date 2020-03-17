<?php session_start();
// linked by dispense.php

include "../conn.php";
//session prod,user_id are set by dispense and login resp
//$_SESSION['user_id'] = '';
if(!empty($_GET['req'])){
	$req = $_GET['req'];
	if(!empty($_SESSION['prod'])){
		if(!empty($_SESSION['user_id'])){
//			echo $_SESSION['user_id'];
			echo requestModule_upr($_SESSION['user_id'],$_SESSION['prod'],$req);//dispense_ipr
		}else{
			echo -17;
		}
	}else{
		echo -16;
	}
}else{
	echo -15;
}

// should handle first request as start and deduct money before starting.
?>