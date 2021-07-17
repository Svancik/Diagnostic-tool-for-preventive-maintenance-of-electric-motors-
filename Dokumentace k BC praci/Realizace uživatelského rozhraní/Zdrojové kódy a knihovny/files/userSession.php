<?php
//Zahájení session uživatele po přihlášení	
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi a přihlašovací údaje
session_start();		//Zahájení session 
if(isset($_SESSION['user'])){
		//Nic se neděje pokud sesssion User je již nastavena
}
else if(isset($_POST['user']) && isset($_POST['pass'])){	//Kontrola zda jsou předané proměnné
	if($_POST['user'] == $User && $_POST['pass']==$Pass){	 //Kontrola přihlašovacích údajů
		$_SESSION['user'] = $User;							  //Zahájení seesion
		echo "<script>location.href='/index.php'</script>";		//Přesměrování na hlavní stránku
	}
	else{
	echo "<script>location.href='/files/login.php?fail=true'</script>";		//Přesměrování na přihlašovací stránku s příznakem fail pro indikaci špatně zadaných údajů
	}			
}
else{
	echo "<script>location.href='/files/login.php'</script>";		//Přesměrování na přihlašovací stránku
}

?>