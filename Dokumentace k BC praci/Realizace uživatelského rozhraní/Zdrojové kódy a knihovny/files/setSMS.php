<?php
//Tento soubor zastřešuje nahrání konfigurace SMS notifikací do databáze
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi
//Načtení předaných hodnot z formuláře do proměnných
$tel = $_POST['tel'];
$Ateplota = $_POST['Ateplota'];
$Aproud = $_POST['Aproud'];
$Avibrace = $_POST['Avibrace'];

$sql = "INSERT INTO smssettings (tel, Ateplota, Aproud, Avibrace) VALUES ('$tel', '$Ateplota', '$Aproud', '$Avibrace');"; //SQL příkaz na vložení proměnných do databáze

mysqli_query($conn, $sql); //Inicializace SQL příkazu

header("Location: /index.php");		//Přesměrování na hlavní stránku
?>

