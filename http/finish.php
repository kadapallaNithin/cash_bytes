<?php
session_start();
include '../conn.php';
if(!empty($_SESSION['prod'])){
	if(!empty($_SESSION['user_id'])){
		echo requestModule_upr($_SESSION['user_id'],$_SESSION['prod'],0);
	}else{
		echo -17;
	}
}else{
	echo -16;
}
?>