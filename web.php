<?php

/*$conn ;
function connect(){
	global $conn;
	$conn = new mysqli("localhost","root","","pipasa");
	if($conn->error){
		die("Connect Error");
	}
}*/
include 'conn.php';
//Triggers are used in mysql so don't transfer again
function pay($amount,$disp,$user,$status,$remarks){

	global $conn;
	connect();
	$prp = $conn->prepare("insert into payments(amount,dispenser,user,status,remarks) values(?,?,?,?,?);");
	if($conn->error)
		return -1;
	$prp->bind_param("issis",$amount,$disp,$user,$status,$remarks);
	$prp->execute();
	if($prp->error)
		return -2;
	$conn->close();
	return 0;
}

function User($name,$username,$phn,$pin){
	global $conn;
	connect();
	$prp = $conn->prepare("insert into user(name,username,phone,pin) values(?,?,?,?);");
	if($conn->error)
		return -1;
	$prp->bind_param("ssss",$name,$username,$phn,$pin);
	$prp->execute();
	if($prp->error)
		return -2;
	$conn->close();
	return 0;
}
/*
 * sign/in.php
 */
function user_id($value,$pin,$username){//by default $value is phone if $username === true, is is username
	$value = sanitize($value);
	$pin = sanitize($pin);
	$loginby = "phone";
	if($username){
		$loginby = "username";
	}
	$sql = "select id from user where ".$loginby." = ? and pin = ? ";
	global $conn;
	connect();
	$prp = $conn->prepare($sql);
	if($conn->error){
		return -1;
	}
	if($username){
		$prp->bind_param("ss",$value,$pin);
	}else{
		$prp->bind_param("is",$value,$pin);
	}
	$prp->execute();
	if($prp->error){
		return -2;
	}
	$rs = $prp->get_result();
	if($rs->num_rows == 1){
		if($row = $rs->fetch_assoc()){
			return $row['id'];
		}else{
			return -3;
		}
	}
	return -4;
}
//echo user_id('966696237','',false);

/*
 * 
 *
 */
function post_paid($user_id,$prod_id){
	global $conn;
	connect();
	$prp = $conn->prepare("select * from post_paid where user = ? and prod = ?");
	if($conn->error)
		return -1;
	$prp->bind_param("ii",$user_id,$prod_id);
	$prp->execute();
	if($prp->error)
		return -2;
	$rs = $prp->get_result();
	$conn->close();
	return $rs->num_rows;
}
//echo post_paid('nithin',1);
?>