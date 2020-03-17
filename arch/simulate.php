<?php 
include_once './conn.php';
function simulate(){
    global $conn;
    connect();
//    $at = 1576059733;
    $rs = $conn->query("select sensor_value,rw,at from remaining order by at desc;");
    $row = $rs->fetch_assoc();
    if($row['rw'] > 0)
        $conn->query("update remaining set sensor_value= sensor_value + 10, rw=rw - 10 where at = ".$row['at'].";");
    //echo $row['rw'];
    return '{"sensor_value":'.$row['sensor_value'].',"at":'.$at.'}';//$row['at'].
}
echo simulate();
?>