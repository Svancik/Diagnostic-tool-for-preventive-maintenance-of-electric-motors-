<?php
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi
$sql = "SELECT COUNT(id) AS total FROM arduino"; //SQL příkaz kterým se získá počet všech ID v tabulce databáze
$result=mysqli_query($conn,$sql); //Inicializace SQL příkazu
$values = mysqli_fetch_assoc($result); //Deklarace proměnn values do které je uložen výsledek
$num_rows=$values['total']; //Uložené počtu všech ID do proměnné počtu řádků

echo json_encode($num_rows); //Převedení do reprezentace JSON a vrácení zpět
exit();