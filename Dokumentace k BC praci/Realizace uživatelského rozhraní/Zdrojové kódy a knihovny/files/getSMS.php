<?php
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi
$sql = "SELECT * FROM smssettings ORDER BY ID DESC LIMIT 1"; //SQL příkaz  na vybrání posledního řátku
$retezecStringu = ""; //Deklarace proměnné do které se uloží data z databáze
$result = mysqli_query($conn, $sql); //Inicializace SQL příkazu
while($row = mysqli_fetch_assoc($result)){  //Cyklus procházející vrácené řádky, v tomto případě se jedná o 1 řádek
	//Data jsou na základě názvu sloupcu sloučena do jednoho textového řetězce který rozděluje oddělovat ve formě středníku. Tento textový řetězec je načten GSM modulem SIM900 Arduina a textový řetězec je rozdělen.
	echo";"; 
	echo $row["tel"]; 
	echo";";
	echo $row["Ateplota"]; 
	echo";";
	echo $row["Aproud"]; 
	echo";";
	echo $row["Avibrace"];
}
?>