<?php
session_start();
$prv = $_SESSION['user_id'];
$_SESSION['user_id'] = '';
$_SESSION['perm'] = '';
echo "<script>window.location.assign('./index.php');</script>";
?>