<?php
// Configure the following
define("SERVER","localhost");
define("USER","root");
define("PASSWORD","");
define("DB","pipasa");
$conn ;
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
/*function user($phone){
	global $conn;
	connect();
	$pr = $conn->prepare("select username from user where phone = ?");
	$phone = sanitize($phone);
	$pr->bind_param("s",$phone);
	$pr->execute();
	$rs = $pr->get_result();
	if($rs->num_rows === 1){
		if($row = $rs->fetch_assoc()){
			$conn->close();
			return $row['username'];
		}
	}
	$conn->close();
	return -1;
}*/
//echo user('9666962037');
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
	echo 'console.log("'.$res.$link.'");';
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
 * http/start.php
 *
function dispense_ipr($user_id,$prod,$req){
    $numP = num_prod($prod);
    if($numP == 1){
	return  requestModule_upr($user_id,$prod,$req);
    }else{
	return -2;
    }
    return -9;
}*/


/*function dispense_upr($user,$prod,$req){
    $num = num_users($user);//can be removed if user is real user.
    $numP = num_prod($prod);
    if($num == 1 ){
        if($numP == 1){
	    $res = requestModule_upr($user,$disp,$req);
	    return $res;
/*	    if( $res == 0){
			//connect();
		//$conn->query("update dispense 
	    }else{
		return $res;
	    } 
*        }else{
            return -2;
        }
    }else{
        return -1;
    }
    return -9;
}
*/

/*            //$xrt = xrt();
	    connect();
    	    global  $conn;
            $rs = $conn->query('select rate from rate where product_id='.$prod.' order by at desc limit 1');
            if(!$conn->error && $row = $rs->fetch_assoc() ){//handle $conn->error
		    if(isset($row['rate'])){
                        $prp = $conn->prepare("insert into dispense(at,name,payment_id,product_id,req,state) values(?,?,?,?,?,?,?);");
                        if(isset($conn->error) == 0 || $conn->error == '' ){
                            $at = time();
                            $rate = $row['rate'];
                            //$req = requiredWater();
                            $product_id = 2;
			    $state = 0;
                            $prp->bind_param("isiiii",$at,$user,$payment_id,$product_id,$req,$state);
                            $prp->execute();
                            if($conn->error || $prp->error){
                                $conn->close();
				return -6;
                            }
			    $conn->close();
                            //$rw = $req;
			    //return requestModule($user,$disp,$req);
                        }else{
                            return -5;
                        }
                    }else{
                        return -4;
                    }
                }else{
                    return -4;
                }*/

function last_id(){
	global $conn;
	connect();
	$rs = $conn->query("select id from sensor_values order by id desc limit 1");
	if($conn->error)
		return -1;
	if($row = $rs->fetch_assoc()){
		$id = $row['id'];
	}else{
		return 0;
	}
	$conn->close();
	return $id;
}
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

/*
 * return 0 => ready
 * xrt is approximate about 0.0000001% error
 */
function xrt() {
     connect();
     global $conn;
     $rs = $conn->query("select xrt from remaining order by at desc limit 1");
     $row = $rs->fetch_assoc();
     if($row['xrt'] <= 0.000001){
	$conn->close();
	return 0;
     }
     $conn->close();
//	echo "<script>xrt".$row['xrt']."</script>";
     return $row['xrt'];
}

?>