<?php session_start() ?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>Oota Water Dispenser</title>
    <link href="/css/materialize.min.css" rel="stylesheet" />
    <link href='./nav.css' rel='stylesheet' />
    <link href='./table.css' rel='stylesheet' />
    <script src='./nav.js' ></script>

    <style>
	.home{
	    margin : 2px;
	    padding:10px;
	    text-align:center;
	    font-size : 30px;
	    background : linear-gradient(lightgreen,skyblue);
	    color : white;
	    box-shadow: 2px 2px 2px grey;
	}
	form{
	    padding : 10%;
	}
	td{
	    padding-right : 15px;
	    background : rgba(0,0,0,0.01);
	    /*border-right : 1px solid green;
	    border-bottom : 1px solid grey;*/
	    box-shadow : 1px 1px 1px grey;
	}
	.th{
		color : rgb(180,20,140);
		background : rgba(170,195,240,0.5);
	}

	
    </style>    

</head>
<body>

    <b style="font-size:30px;color:yellow;position:fixed;left:10px;top:15px;z-index:1;" onclick="openNav('mySidenav')">&#9776;</b>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="document.getElementById('mySidenav').style.width = '0px'">&times;</a>

        <a href="./gp/index.html"><!--img src="threeLions" class="navImg"/-->Grama Panchayat</a>
        <a href="./school/index.html"><!--img class="navImg" src="sar.png" /--> &nbsp;School</a>
        <a href="./tourist/index.html">Tourist Places</a>
        <a href="./swadhyaya/index.html"><!--img src="logo.jpg" class="navImg" /-->Swadhyaya</a>
        <a href="./youth/index.html"><!--img src="vivekananda.jpg" class="navImg"/-->Youth Associations</a>
        <a href="./kaikilikavali/index.html">Kaikili Kavali</a>
	<a href="./log<?php if(empty($_SESSION['user_id'])) echo 'in.php" > Login </a>'; else echo 'out.php" > Logout </a>'; ?>
    </div>
<div class='home'>Oota <br /><small style='font-size:18px'>Water Dispenser <?php if(isset($_SESSION['user_id'])) echo $_SESSION['user_id']; ?></small></div>

	<form action='dispense.php' method='GET'>	
	
	<div class="row">
		<div class='card-panel col s6 offset-s3'>
			<table>
				<tr class='th'><td>Type</td><td>Rate (Rupees)</td></tr>
				<tr><td>1 liter</td><td>2.3</td></tr>
				<tr><td>20 liter</td><td>40</td></tr>
			</table>
		</div>    
	<div class="card-panel center col s6 offset-s3" style="background:skyblue;color:white;height:40px"> <b>Water is available</b></div>
	    <input name='prod' type='hidden' class='col offset-s3 s6' value=1 required=True />
            <input type='submit' class="col btn offset-s10 s2 blue" style="color:white;min-width:120px;" value='Dispsense'/>
            <!--button id="fin" class="col btn s2 red" disabled onclick="">Finish</button-->
    </div>

	</form>    
    <script>
//	alert('j');
	function start(){
	    var link = './dispense.php?prod='+document.getElementById('prod').value;
	    window.location.assign(link);
	}
    </script>
</body>
</html>