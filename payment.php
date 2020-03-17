<?php session_start();
include 'web.php';
if(empty($_SESSION['user_id'])){
//$_SESSION['user'] = 'nithin';
	echo "<script>window.location.assign('./login.php?back='+window.location.href);</script>";
}else if(empty($_SESSION['perm'])){
	if(empty($_SESSION['prod'])){
		echo "prod not defined ";
	}else{
		if(post_paid($_SESSION['user_id'],$_SESSION['prod']) === 1){
			$_SESSION['perm'] = 'on';
			if(!empty($_GET['back'])){
				echo "<script>window.location.assign('".$_GET['back']."');</script>";
			}else{
				echo "<script>window.history.back();</script>";
			}
		}else{
			echo "not Post Paid";
		}
	}
}else{
	echo "<script>window.location.assign('./rates.php');</script>";
}
echo '<br />payment page';
?>