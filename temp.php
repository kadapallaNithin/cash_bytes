<?php
// Configure the following
define("SERVER","localhost");
define("USER","root");
define("PASSWORD","");
define("DB","pipasa");
$conn;
function connect(){
	global $conn;
	$conn = new mysqli(SERVER,USER,PASSWORD,DB);
	
	if($conn->connect_error){
	    die("Connection error: ".$conn->connect_error);
	}
}
function sanitize($var){
    return $var;
}
function check_login(){
	if(!empty($_SESSION['username'])){
		echo "<script>window.location.assign('/sign/in.php')";
	}
}
/* $user - username , return # of users with given username
 * return  = 1 means a real user.
 *      -1 error
 */
function num_users($user){
	connect();
	global $conn;
	$prp = $conn->prepare("select username from user where username = ?");
	$prp->bind_param('s',$user);
	$prp->execute();
	$rs = $prp->get_result();
 	if($prp->error){
	    $conn->close();
	    return -1;
	}
	$conn->close();
	return $rs->num_rows;
}
/*
 * 
 */
function num_users_p($username,$pin){
	connect();
	global $conn;
	$username = sanitize($username);
	$pin = sanitize($pin);
	$prp = $conn->prepare("select username from user where username = ? and pin = ?");
	$prp->bind_param('ss',$username,$pin);
	$prp->execute();
	$rs = $prp->get_result();
 	if($prp->error){
	    $conn->close();
	    return -1;
	}
	$conn->close();
	return $rs->num_rows;	
}
//echo num_users_p('nithind','1238');
/*
 * $prod uct id
 * return = 1 means a real product.
 *      -1 error
 */
function num_prod($prod){
	global $conn;
	connect();
	$prp = $conn->prepare("select id from products where id=?");
	if(empty($conn->error)){
		$prp->bind_param("i",$prod);
		$prp->execute();
		$rs = $prp->get_result();
		if(empty($prp->error)){
			$conn->close();
			return $rs->num_rows;
		}
	}
	$conn->close();
	return -1;
}
//echo num_prod(1);
/*
 * http/start.php  //dispense_ipr
 * http/finish.php
 */
//requestModule_upr(1,1,0);
function requestModule_upr($user,$prod,$req){
    $numP = num_prod($prod);
    if($numP == 1){
	$notFail = ($link = @file_get_contents('http://skin-lime.000webhostapp.com/api/get_link.php?key=temp&prod='.$prod));
	if(!$notFail){
		return -12;
	}
	$link = json_decode($link,true);
	if($req > 0){
		$f = '/turn?';
		$param = '&req='.$req;
	}else{
		$f = '/finish?';
		$param = '';
	}
	$link = 'http://'.$link['ip'].$f.'key='.$link['api_key'].$param.'&user='.$user;
	$notFail = ($res = file_get_contents($link));
	if(!$notFail){
		return -13;
	}
	//echo 'console.log("'.$res.$link.'");';
	$res = json_decode($res,true);
	if($req > 0 && !empty($res['req'])&& !empty($res['user']) && $res['req'] == $req && $res['user'] == $user){
		return 0;
	}else if(!empty($res['rem'])){
		return $res['rem'];
	}else if(!empty($res['finish'])){
		return $res['finish'];
	}
		return -15;
    }else{
	return -2;
    }
    return -14;
}
//echo requestModule(12,'nithin',1);
/*
 * http/getX.php
 */
function last_at(){
	global $conn;
	connect();
	$rs = $conn->query("select at from sensor_values order by at desc limit 1");
	if($conn->error)
		return -1;
	if($row = $rs->fetch_assoc()){
		$at = $row['at'];
	}else{
		return 0;
	}
	$conn->close();
	return $at;
}
?>