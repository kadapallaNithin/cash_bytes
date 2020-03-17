<?php
//include 'calc.php';
include 'conn.php';
if(ready() == 1){
$rs = $conn->query("select xrt,rw from sensor order by at desc limit 1;");
$row = $rs->fetch_assoc();
echo $row['xrt'].$row['rw'];
}
?>