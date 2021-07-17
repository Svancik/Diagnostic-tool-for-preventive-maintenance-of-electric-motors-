<?php
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi

$sql = "SELECT * FROM smssettings ORDER BY ID DESC LIMIT 1"; //SQL příkaz který z tabulky smssettings načte poslední řádek
$data = array();	//Deklarace pole
$result = mysqli_query($conn, $sql); //Inicializace SQL příkazu

while($row = mysqli_fetch_assoc($result)){ //Cyklus procházející vrácené řádky, v tomto případě se jedná o 1 řádek
	array_push($data, $row);	//Vložení řádku do pole

}
	echo json_encode($data);  //Převedení do reprezentace JSON a vrácení zpět
?>