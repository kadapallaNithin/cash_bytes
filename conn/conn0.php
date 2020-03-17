<?php
//$thre = 2*60;
define("CONSTANT",1);
define("SERVER","localhost");
define("USER","root");
define("PASSWORD","");
define("DB","kurthi");

$conn = new mysqli(SERVER,USER,PASSWORD,DB);

if($conn->connect_error){
    die("Connection error: ".$conn->connect_error);
}


/* $user - username , return # of users with given username
 *  = 0 means not a real user.
 */
function num_users($user){
	global $conn;
	$prp = $conn->prepare("select name from user where username = ?");
	$prp->bind_param('s',$user);
	$prp->execute();
	$rs = $prp->get_result();
	return $rs->num_rows;
}


/*
 * $user - username
 * return  0 : not authenticated
 	   1 : authenticated
 */
function user($user){
	if(num_users($user) == 1){
	}	
}


/* $user - username, 
 * $rd == 0 : ready value is required. 
	  1 : ready value is not required => forced to start or it is implicitly ready.
 * return 1 : ok
	wait time else	
*/
function start($user,$rd){
	global $conn;
	$num = num_users($user);//can be removed if user is real user.
	if($rd == 0){
		$rd = ready();
	}
	if($num == 1 && $rd == 1){
		$prp = $conn->prepare("insert into start(name,at) values(?,?)");
		echo $conn->error;
		$at = time();
		$prp->bind_param("si",$user,$at);
		$prp->execute();
		echo $conn->error;
		echo $prp->error;
		if(dispense() == 1){
		    return 1;
		}
		//echo 'hi';
		return $num;//0
	}
	if($rd == 1){
	   return 2;
	}
	return $rd;
}

/*
function ready(){
	global $conn;
	global $thre;
	$rs = $conn->query('select at from start order by at desc limit 1;');
	$row = $rs->fetch_assoc();
	echo $conn->error;
	$re = time() - $row['at'] - $thre;
	if($re > 0){
		return 1;
	}
	return -$re;
}
*/

function ready(){
    global $conn;
    $rs = $conn->query('select xrt from remaining order by at desc limit 1;');
    $row = $rs->fetch_assoc();
    echo $conn->error;
    $re = $row['xrt'];
    if($re == 0){
        return 1;
    }
    return $re;
}

function test(){ 
/*ready();
	$st = start('nithin',0);
	echo $st;
	if($st != 1){
		echo 'User is not unique';
	}*/
	$temp = 8;
	while($temp>0){
	    $temp = $temp -1;
	    sleep(3);
	}
}
//test();


//WATER DISPENCE CONTROL


function control(){
// handle waits between api calls
//authenticate
}

/*
 * return : array of 'rw' , 'xrt' 
	both are -1 on error.
 */
function remaining(){
	global $conn;
	$rs = $conn->query("select rw,xrt from remaining order by at desc limit 1");
	if(!$conn->error && $row = $rs->fetch_assoc()){
		return $row;
	}
	$temp = array('rw'=>-1,'xrt'=>-1);
	return $temp;
}
function dispense(){
    global  $conn;
    $rs = $conn->query('select rate from rate order by at desc limit 1');
    if($row = $rs->fetch_assoc() && isset($_SESSION['user'])){
        $prp = $conn->prepare("insert into dispense values(?,?,?,?);");
        echo $conn->error;
        $at = time();
        $name = $_SESSION['user'];
        $rate = $row['rate'];
        $amt = 5000;
        $prp->bind_param("siii",$name,$at,$amt,$rate);
        $prp->execute();
//        echo "dispensed";
        echo $conn->error;
        echo $prp->error;
/*      $rs = $conn->query('select rw from remaining order by at desc limit 1');
        if($row = $rs->fetch_assoc()){
            if($row['rw'] <= 0){
                
            }
        }else{
            echo $conn->error.' ';
        }

        $rw = 5;//$amt;
        while($rw > 0){
            sleep(3);
            //$rw are passed for simulation
            $rw = setRemainingSim($rw);
            //echo $rw.'hi this is ';
            //$rw = $rw - 1;
        }
        if($rw == 0){
            sleep(1);
            setRemainingSim(0);
        }else{
            echo "Error Remaining";
        }
*/
        $rw = 50;
        while($rw > 0){
            sleep(1);
            $rw = setRemaining();
        }
        return 1;
    }else{
        echo  'user not logged in or no rate defined';
    }
    return 0;
}

function setRemainingSim($rw){
    global $conn;
    //get from bolt
    $at = time();
    $xrt = $rw*4;// assumed flow rate is 4
    //$rw = 
    $conn->query("insert into remaining values(".$xrt.",".$rw.",".$at.");");
    echo $conn->error;
}
/*
 * m = flowRate (ml/sec)
 * x = time from now (sec)
 * y = water flown at x (ml)
 * y = mx; since nothing is flown now
 * x = y/m = rw / flowRate
 */

function setRemaining(){
    global $conn;
    //get from bolt
//    $res = file_get_contents('http://cloud.boltiot.com/remote/94bf810f-a915-4ea9-8cc2-1c4c8fef2932/digitalWrite?pin=0&state=HIGH&deviceName=BOLT6094614');
    //include './simulate.php';
    //$res = simulate();
    $res = file_get_contents("http://naaperu.ml/php/background/get.php");
    echo $res;
    
    $present = json_decode($res,true);
    $last = $conn->query("select sensor_value,at from remaining order by at limit 1;");
    $last = $last->fetch_assoc();
    $flowRate = (($last['sensor_value'] - $present['sensor_value'])*CONSTANT);//($present['at']-$last['at']);
    $rw = $present['sensor_value']*CONSTANT;//CONSTANT = 
    $xrt = $rw / $flowRate;
    $at = time();
    //$xrt = $rw*4;// assumed flow rate is 4
    //$rw =
    $present['sensor_value'] = 30;
    //echo $present['sensor_value'].'sen';
    $conn->query("insert into remaining values(".'0'.",".$rw.",".$at.",".$present['sensor_value'].");");
    echo $conn->error;
}

//echo start('nithin',0);
?>