<?php //session_start();
?>
<?php
// Configure the following
define("CONSTANT",1);
define("LIMIT",6000);// limit for setRemaining while loop in dispense function
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
 * $disp enser name
 * return = 1 means a real dispenser.
 *      -1 error
 */
function num_disp($disp){
	connect();
	global $conn;
    $prp = $conn->prepare("select username from dispenser where username = ?");
    $prp->bind_param('s',$disp);
    $prp->execute();
    $rs = $prp->get_result();
    if($prp->error){
	$conn->close();
        return -1;
    }
    $conn->close();
    return $rs->num_rows;
}

function num_prod($prod){
	global $conn;
	connect();
	$prp = $conn->prepare("select id from products where id=?");
	$prp->bind_param("i",$prod);
	$rs = $prp->get_result();
	if(empty($prp->error)){
		$conn->close();
		return $rs->num_rows;
	}
	$conn->close();
	return -1;
}
/*
 * return : array of 'rw' , 'xrt'
 both are -1 on error.
 */
function remaining(){
    connect();
    global $conn;
//    echo "reamaining called";
    $rs = $conn->query("select rw,xrt from remaining order by at desc limit 1");
    if(!$conn->error && $row = $rs->fetch_assoc()){
	$conn->close();
        return $row;
    }
    $temp = array('rw'=>-1,'xrt'=>-1);
    	$conn->close();
	return $temp;
}
//echo dispense('nithin','nithin',2);
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
function isconnected(){
	
	return 1;
}
/*function requestModule($user,$disp,$req){
 	//$id = file_get_contents('http://skin-lime.000webhostapp.com/api/client.php');
	$notFail = ($link = @file_get_contents('http://skin-lime.000webhostapp.com/get_link.php?key=temp&disp='.$disp));//id='.$id);//ip.php');//http://localhost/kadapallanithin/pipasa/kendram/ip.php');//
	if(!$notFail){
		return -12;
	}
	//$f = fopen('http://skin-lime.000webhostapp.com/get_link.php?key=temp&disp='.$disp ,"r") or die("Connection failed");
	/*if(empty($link) || $link == -1)
		return -1;
	*/
	$notFail = ($res = file_get_contents('http://'.$link.'&req='.$req.'&user='.$user));//'http://'.$ip.'/start?apikey=');//http://localhost/kadapallanithin/pipasa/kendram/simulate.php');//('
	//echo $link;
	//$res = "{\"req\":1,\"user\":12}";
	if(!$notFail){
		return -13;
	}
	$res = json_decode($res,true);
	//echo $res;
	if(!empty($res['req'])&& !empty($res['user']) && $res['req'] == $req && $res['user'] == $user){
		return 0;
	}else if(!empty($res['rem'])){
		return $res['rem'];
	}
	return -1;
}*/
function requestModule_upr($user,$prod,$req){
	$notFail = ($link = @file_get_contents('http://skin-lime.000webhostapp.com/get_link.php?key=temp&prod='.$prod));
	if(!$notFail){
		return -12;
	}
	$notFail = ($res = file_get_contents('http://'.$link.'&req='.$req.'&user='.$user));
	if(!$notFail){
		return -13;
	}
	$res = json_decode($res,true);
	if(!empty($res['req'])&& !empty($res['user']) && $res['req'] == $req && $res['user'] == $user){
		return 0;
	}else if(!empty($res['rem'])){
		return $res['rem'];
	}
	return -14;
}
//echo requestModule(12,'nithin',1);
/*function moduleRemaining(){
	$res = file_get_contents('http://'.$
}*/
function setRemainingInit($rem){
	global $conn;
        connect();
        $conn->query("insert into remaining values(0,1,".$rem.",".time().",0);");
	if($conn->error)
		return -1;
	$conn->close();
	return 0;
}
function requiredWater(){
	return 300;
}


function dispense_upr($user,$prod,$req){
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
*/        }else{
            return -2;
        }
    }else{
        return -1;
    }
    return -9;
}
 
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


/*function dispense($user,$disp,$req){
    $num = num_users($user);//can be removed if user is real user.
    $numD = num_disp($disp);
    if($num == 1 ){
        if($numD==1){
            //$xrt = xrt();
	    $res = requestModule($user,$disp,$req);
	    if( $res == 0){
		
	    }else{
		return $res;
	    } 

//$rm = remaining();
//            if($rm['rw']== 0 && isconnected()){//xrt == 0){
		connect();
    		global  $conn;
                $rs = $conn->query('select rate from rate where disp="'.$disp.'" order by at desc limit 1');
                if(!$conn->error && $row = $rs->fetch_assoc() ){//handle $conn->error
                    //echo "hey";
		    if(isset($row['rate'])){
                        $prp = $conn->prepare("insert into dispense values(?,?,?,?,?);");
                        if(isset($conn->error) == 0 || $conn->error == '' ){
                            $at = time();
                            $rate = $row['rate'];
                            //$req = requiredWater();
                            $product_id = 2;
                            $prp->bind_param("siiii",$user,$at,$req,$product_id,$payment_id);
                            $prp->execute();
                            if($conn->error || $prp->error){
                                $conn->close();
				return -6;
                            }
			    $conn->close();
                            $rw = $req;
//                            if(setRemainingInit($req) == -1)
//				return -11;
                            /*while($rw > 0 && (time() - $at < LIMIT)){
                                sleep(1);
                                $rw = setRemaining($req);
                                if($rw < 0){
                                    return -8;
                                }
                            }
			    
                            if(time() - $at >= LIMIT){
                                return -7;
                            }*return 0;*/
			    return requestModule($user,$disp,$req);
                        }else{
			    //echo "error is ".$conn->error;
                            return -5;
                        }
                    }else{
                        return -4;
                    }
                }else{
                    return -4;
                }
            //}else if($rm['rw'] == 0){
//		return -10;
//	    }else{
//                return -3;//array(-3,$xrt);
//		return $rm['rw']; //$xrt; 
//            }
        }else{
            return -2;
        }
    }else{
        return -1;
    }
    return -9;
}
*/

/*
 * user
 * disp enser
 * req uired quantity of water // validate
 * return //array(-3,xrt) or array(code)* codes are as
 *	   0 on success 
 * 
 *        -1 user not real
 *        -2 disp not real
 *        rw remaining water //xrt xrt != 0 // -3 for state not mensioned in get.php
 *        -4 rate not defined or sql error
 *        -5, -6 dispense sql error
 *        -7 limit crossed for transaction
 *        -8 could not request module //flowRate == 0 or unknown error
 *        -9 unknown error
 *        -10  not connected
 */

/*function dispense($user,$disp,$req){
    $num = num_users($user);//can be removed if user is real user.
    $numD = num_disp($disp);
    if($num == 1 ){
        if($numD==1){
            //$xrt = xrt();
	    $res = requestModule($user,$disp,$req);
	    if( $res == 0){
		
	    }else{
		return $res;
	    } 

$rm = remaining();
            if($rm['rw']== 0 && isconnected()){//xrt == 0){
		connect();
    		global  $conn;
                $rs = $conn->query('select rate from rate where disp="'.$disp.'" order by at desc limit 1'); 
                if(!$conn->error && $row = $rs->fetch_assoc() ){//handle $conn->error
                    //echo "hey";
		    if(isset($row['rate'])){
                        $prp = $conn->prepare("insert into dispense values(?,?,?,?,?);");
                        if(isset($conn->error) == 0 || $conn->error == '' ){
                            $at = time();
                            $rate = $row['rate'];
                            //$req = requiredWater();
                            $product_id = 2;
                            $prp->bind_param("siiii",$user,$at,$req,$rate,$product_id);
                            $prp->execute();
                            if($conn->error || $prp->error){
                                $conn->close();
				return -6;
                            }
			    $conn->close();
                            $rw = $req;
//                            if(setRemainingInit($req) == -1)
//				return -11;
                            /*while($rw > 0 && (time() - $at < LIMIT)){
                                sleep(1);
                                $rw = setRemaining($req);
                                if($rw < 0){
                                    return -8;
                                }
                            }
			    
                            if(time() - $at >= LIMIT){
                                return -7;
                            }*return 0;*
			    return requestModule($user,$disp,$req);
                        }else{
			    //echo "error is ".$conn->error;
                            return -5;
                        }
                    }else{
                        return -4;
                    }
                }else{
                    return -4;
                }
            }else if($rm['rw'] == 0){
		return -10;
	    }else{
//                return -3;//array(-3,$xrt);
		return $rm['rw']; //$xrt; 
            }
        }else{
            return -2;
        }
    }else{
        return -1;
    }
    return -9;
}*/
/*
 * m = flowRate (ml/sec)
 * x = time from now (sec)
 * y = water flown at x (ml)
 * y = mx; since nothing is flown now
 * x = y/m = rw / flowRate
 * 
 * return 
 *      +ve => $rw
 *      -1 required amount of water is not set
 *      -2 flowrate = 0
 *      
 */

function setRemaining($req){
    //get sensor_value from bolt
    $res = file_get_contents('http://skin-lime.000webhostapp.com/api/bye.php');//'http://cloud.boltiot.com/remote/94bf810f-a915-4ea9-8cc2-1c4c8fef2932/digitalWrite?pin=0&state=HIGH&deviceName=BOLT6094614');
    //$res = sessSimulate();
    $present = json_decode($res,true);
    connect();
    global $conn;
    $last = $conn->query("select sensor_value,at from remaining order by at limit 1;");
    $last = $last->fetch_assoc();
    if(isset($present['sensor_value'],$present['at'],$last['sensor_value'],$last['at']) && $last['at'] != $present['at']){
        $at = $present['at'];// time();//+0.000000000001;
        $flowRate = (($present['sensor_value'] - $last['sensor_value'])* CONSTANT )/($at-$last['at']);//($present['at']-$last['at']);
        /*if(isset($_SESSION['req'])){
            $rw =$_SESSION['req'] - $present['sensor_value']*CONSTANT;//CONSTANT =
        }else{
            return -1;
        }*/
        echo "flow rate ".$flowRate."<br />".($at - $last['at']);
        $rw = $req - $present['sensor_value']*CONSTANT;
        
        if($flowRate == 0){
            return -2;
        }
        if($rw < 0){
            $rw = 0;
        }
        $xrt = $rw / $flowRate;
        $conn->query("insert into remaining values(".$xrt.",".$rw.",".$at.",".$present['sensor_value'].");");
        if(!$conn->error){
	    $conn->close();
            return $rw;
        }
    }
    $conn->close();
    return -1;
}
/*
 * returns 0 success
 *	  -1 failure
 */
function finish(){
	setRemainingInit(0);
	return 0;
}
function last_id(){
global $conn;
connect();
$rs = $conn->query("select id from sensor_values order by id desc limit 1");
if($conn->error)
return -1;
if($row = $rs->fetch_assoc()){
$id = $row['id'];
}
$conn->close();
return $id;
}
/*
 * return {"sensor_value":xx,"at":xx} // at is included since there can be intolerable error because of bandwidth delay
 */
function sessSimulate(){
    if(!isset($_SESSION['sim'])){
        $_SESSION['sim'] = 100;
    }
    $_SESSION['sim'] = $_SESSION['sim'] + 10;
    return '{"sensor_value":'.$_SESSION['sim'].',"at":'.time().'}';
}
?>