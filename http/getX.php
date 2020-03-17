<?php
session_start();
include '../conn.php';
//$count_per_ml = 400;
//$req = 400;
//$rm = remaining();
$from = last_at();
$from = str_replace(" ","%20",$from);
$json = file_get_contents('https://skin-lime.000webhostapp.com/api/bye.php?from='.$from);
$rs = json_decode($json,true);
connect();
$prp = $conn->prepare("insert into sensor_values(product,ip,value,at,module_at) values(?,?,?,?,?);");
if($conn->error){
	die('{"error":'.'1'.$conn->error.'}');
}
$prp->bind_param("isisi",$id,$ip,$value,$at,$module_at);
if($prp->error){
	die('{"error":'.'2'.$prp->error.'}');
}
for($i = 1; $i < count($rs); $i++){
	$at = $rs[$i]['at'];//."'";
	//echo "at : ".$at." from ".$from;
	if($at != str_replace("%20"," ",$from)){
		$ip = $rs[$i]['ip'];
		$id = $rs[$i]['id'];
		$value = $rs[$i]['value'];
		$module_at = $rs[$i]['module_at'];
//	echo $at;
		$prp->execute();
		if($prp->error){
			die('{"error":"'.'3'.$prp->error.'"}');
		}
	}
}
echo '{"error":0,"fw":'.$rs[$i-1]['value']/$count_per_ml.'}';//"cpml":2
//$a = 200;$d = 5;echo '{"error":0,"fw":'.$a/$d.'}';
//echo '{"rw":'.$rm['rw'].',"xrt":'.$rm['xrt'].'}';
?>