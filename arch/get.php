<?php session_start();
/*
Parameters:
state	= 0 start
	= 1 middle
	= 2 finish
Response: 
return 	= 0  ok
       	= 1  not ok because device is busy
       	= 2  not ok because user is not authenticated
       	= 3  not ok because state is not mentioned
rem 	: remaining amount of water to be dispensed.
xrt	: expected remaining time for completion of transaction.
*/
include "./conn.php";
if(isset($_GET['state']) ){
     if(isset($_SESSION['user'])&& num_users($_SESSION['user']) == 1){
         $res = remaining();
	if($_GET['state'] == 0 ){

		/*if(isset($_SESSION['start'])){
			echo $_SESSION['start'];
			echo '{"return":1}';
			
		}else{*/
	    
		if($res['rw'] == 0){
		    
		    $st = start($_SESSION['user'],0);
		   
			//$_SESSION['start'] = time();
		    if($st > 0){
		        echo '{"rem":'.$st.',"return":0}';
		    }else{
    		    echo '{"rem":'.$res['rw'].',"xrt":'.$res['xrt'].',"return":1,"wait":'.$st.'}';
		    }
		//}
		}else{
		    echo '{"xrt":'.$res['xrt'].',"return":1}';
		}
	}else if($_GET['state'] == 1){
		//$res = remaining();
		echo '{"xrt":'.$res['xrt'].',"rw":'.$res['rw'].',"return":0}';
	}else if($_GET['state']==2){
		echo '{"xrt":0,"return":0}';
	}
    }else{
	echo '{"return":2}';
    }
}else{
//echo 'state not defined or not a user';
echo '{"return":3}';
}// should handle first request as start and deduct money before starting.
?>