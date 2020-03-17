<?php session_start(); ?>
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title></title>
    <style>
	div{
	    margin : 2px;
	}
	button{
	    margin : 10px;
	    padding : 200px;
	}
    </style>
    <link href="/css/materialize.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="center">Water Dispenser <?php if(!empty($_SESSION['user_id'])) echo $_SESSION['user_id']; ?></div>
        </div>
    </div>
    <div class='container'>
	<div class='row'>
		<div class='col s5 offset-s1'>Remaining<br /><b id='rem'></b></div>
		<div class='col s5 offset-s6'>Filled<br /><b id='fw'></b></div>
    </div>
	</div>
    	<div>
		<input id='req' type='number' value=1000 />
		<select style='background:green'>
			<option value='l'>liters</option>
			<option value='ml'>ml</option>
		</select>
		HI                 
	</div>
	<div class="row">
        <!--div class="card-panel center col s6 offset-s3" style="background:skyblue;color:white;height:40px"> <b>Water is available</b></!--div-->
        <span>
            <button id="str" class="col btn offset-s4 s2" onclick="strt()" style="color:white">Start</button>
	    <!--button id='get' class="col btn s2 blue" onclick='get()'>Get values</button-->
            <button id="fin" class="col btn s2 red" disabled onclick="finish()">Finish</button>
        </span>
     </div>
    <script>
	var perm = '<?php 
if(empty($_GET["prod"])){ 
	echo ''; 
}else{  
	if(empty($_SESSION["perm"])){
		echo '';
	}else{
		echo '1';
	}
	$_SESSION["perm"] = "" ; 
	$_SESSION["prod"] = $_GET['prod'];
}
?>';
	if(perm == ''){
//		alert('First complete payment or select product');
		window.location.assign('./payment.php?back='+window.location.href);
	}
    	var x,y,start,req;//, tem = 3;
    	if(window.XMLHttpRequest){
    		start = new XMLHttpRequest();
    		x = new XMLHttpRequest();
    	}else{
    		start = new ActiveXObject("Microsoft.XMLHTTP");
    		x = new ActiveXObject("Microsoft.XMLHTTP");
   	}
   	start.onreadystatechange = function (){
//		alert(start.readyState+' '+start.status+' '+start.responseText);
		if(start.readyState==4 && start.status == 200){
			var res = start.responseText;
			document.getElementById('rem').innerHTML = res;
			console.log(res);
			//res = JSON.parse(res);//start.responseText);//if(res['return'] == 1){
//			alert(res);
			if(res > 0){
				waitCount(res);
			}else if(res < 0){
				alert("error"+res);//error[-res]);
			}else{
			    //req = res.req;
//			    console.log(res);
//			    y = setInterval(get,1000);
			}
		}
	};

	x.onreadystatechange = function(){
//		console.log(x.readyState);
   		if(x.readyState == 4 && x.status == 200){
   	   		//console.log(x.responseText);
			var resT = x.responseText;
			console.log(resT);
			if(resT[0] !== '{'){
				resT = resT.substring(1);
			}
			var res = JSON.parse(resT);
//            		console.log(res);
			if(res.error === 0){
				/*if( res.rw > 0 ){// && tem > 0){ res.return === 0
        	                	//tem = tem - 1;
				}else{
					console.log(res);
					//alert(res.return);
					clearInterval(y);
				}*/
				if(res.fw >= req ){
					console.log(res.fw);
					clearInterval(y);
					alert('Thank you :) ');
					document.getElementById('fin').disabled = true;
				    	document.getElementById('str').disabled = false;
					//window.location.assign("./index.php"); 
				}//else{
				document.getElementById('rem').innerHTML = req - res.fw;//res.rw;
				
				document.getElementById('fw').innerHTML = res.fw;//req - res.rw;
				//}
			}else{
				document.getElementById('rem').innerHTML = "Sorry Error occured try again.";
		//		clearInterval(y);
				console.log(res.error);
			}
		}
	};		
        
    	function strt(){
        	console.log('strt called');
        	document.getElementById('str').disabled = true;
        	document.getElementById('fin').disabled = false;
		req = document.getElementById('req').value; 
		start.open("GET",'./http/start.php?req='+req,true);
		start.send();
//		y = setInterval(get,1000);
	}
	function get(){
		x.open("GET",'./http/getX.php',true);
		x.send();
	}
	function finish(){
		if(confirm("wish to finish?")){
//        		clearInterval(y);
			x.open("GET",'./http/finish.php',true);
        		x.send();
        	//clearInterval(y);
			document.getElementById('fin').disabled = true;
		    	document.getElementById('str').disabled = false;
/*        		alert("Finished");
			window.location.assign('./index.php');
*/		}
    	}
	function waitCount(res){
		alert('Wait for approx. '+Math.floor(res/60)+' minutes and '+Math.floor(res%60)+'seconds');//Math.floor(res['wait']/60)+' minutes and '+res['wait']%60+'seconds');
	}
    </script>
</body>
</html>