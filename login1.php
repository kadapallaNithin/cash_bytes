<?php
include 'conn.php';
connect();
$prp = $conn->prepare("select * from user where username='nithin' and pin = ?");
$prp->bind_param("s",$p);
$p = '';
$prp->execute();
$rs = $prp->get_result();
$row = $rs->fetch_assoc();
echo $row['username'];
?>
<script src='/js/js.js'></script>