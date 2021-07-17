<?php
//Tento soubor obsahuje přihlašovací formulář a je na něj uživatel odkázán v momentě kdy zadá špatné přihlašovací údaje
if(isset($_GET['fail'])){
	echo "<script>alert('Špatné údaje, zadejte znovu');</script>";		//Pokud je předát parametr fail tak uživatel je v okně upozorněn na špatně zadané údaje
}
?>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style>
	body{background-color:#5D656F; font-size:24pt; 	}
	.logform{margin:auto; text-align:center; border-style: solid; background-color:white;  margin: auto; width:35%; margin-top:10%;padding-top: 15px;}
	input{  width:80%;}
	@media only screen and (max-width: 768px) {form{width:90%;}.logform{ width:90%;	}} /*Jiné styly pro zobrazení na zařízení s menším rozlišením 
</style>	
</head>
<body>
<div class="logform"> 
	<form action ="/index.php" method ="POST">	<!--Přihlašovací formulář odkazující na index.php-->
	   <h4>Přihlašovací jméno: </h4>
	  <input type="text"  name="user" placeholder="username"><br><br>
	  <h4>Heslo: </h4>  
	  <input type="password"  name="pass" placeholder="password"><br><br>
	  <input type="submit" id ="btn" value="Odeslat">
	</form>
</div>
</body>