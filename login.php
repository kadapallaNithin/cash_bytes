<?php
session_start();
$rout = '';//../../pipasa/kendram/';
include $rout.'web.php';
//include '/util.php';
$username = '';
$phone = '';
echo "<script>";
function in(){
	global $rout;
	if(!empty($_POST['pin'])){
//		echo "console.log('Pin is ".$_POST['pin']." ');";
		if($_POST['loginby'] === 'Phone number' && !empty($_POST['phone'])){
			$id = user_id($_POST['phone'],$_POST['pin'],false);
		}else if($_POST['loginby']=== 'Username' && !empty($_POST['username'])){
			$id = user_id($_POST['username'],$_POST['pin'],true);
		}else{
			echo "alert('Enter username or phone number');";
		}
		//if(!empty($user_id) && !empty($type)){
			//if(num_users_i_p($user_id,$_POST['pin']) === 1 ){
			//	$_SESSION['username'] = $username;
		if(!empty($id)){
			if($id < 0 ){
				echo "console.log('Recieved ".$_POST['username'].$_POST['phone']." , ".$_POST['pin']." and error is ".$id."');";
				echo "alert('Incorrect ".$_POST['loginby']." or pin ');";
			}else{
				$_SESSION['user_id'] = $id;
				echo "console.log('logged in ".$id.",".$_POST['pin']."');";
				$loc = $rout.'index.php';
				if(!empty($_GET['back'])){
					$loc = $_GET['back'];
				}
				echo "window.location.assign('".$loc."');";
			}
		}
	}else{
		echo "alert('Please Enter pin');";
	}
}
if(isset($_POST['post'])){
	in();
}
echo "</script>";

?>

<!DOCTYPE html>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='/css/style.css' rel='stylesheet' />
    </head>

<body><center>
<div><br /><br />
    <form style="border:1px solid #aaa;border-radius:15px;position:relative;left:20%;width:60%;" id="signUp" action="login.php<?php if(!empty($_GET['back'])) echo '?back='.$_GET['back']; ?>" method="post">
        <h1>Sign In here</h1>
        <hr>
        <br>
        
        <br><b><input id='phone_opt' onchange='setPhone()'  type="radio" name="loginby" checked=true value='Phone number'>Phone Number </b>
        <b> <input id='usr_opt' onchange='setUsername()' type ="radio" name="loginby"  value='Username' >    Username<br/></b><br>
        <input type="number" id="phn" maxlength='10' onkeyup="validatePhone()" placeholder="Enter your phone number" name="phone"  value="<?php echo $phone?>"/><span id="phnval">:(</span>
        
        <input type="text" id='username' maxlength='63' style="display:none;" placeholder="Enter your username " name="username" value="<?php echo $username ?>">
	<input type='hidden' value='true' name='post' />
    <br /><br />

        <b>Pin:</b>
        <input type='password' maxlength='6' name='pin' placeholder='Enter your pin here ' required=True > 
        <br>
        <div>
            <input id='bye' type="button" onclick="window.location.assign('../bye.html');" value="Cancel">
            <input id="sub" onclick="check_sub()" style="background: grey;opacity:0.3" type="submit" value="submit" disabled>
        </div>
    </form></div>
    <br>Forgot pin? <a href="./reset.php">Reset pin</a>
    <br />Don't have an account? 
    <a href="./up.php">sign up</a><br />
    </center>
    <script src='/js/js.js'>
	validatePhone();	
    </script>
    </body>
</html>