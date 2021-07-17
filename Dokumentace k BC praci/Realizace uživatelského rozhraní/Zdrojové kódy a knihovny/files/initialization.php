<?php
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi
$result = mysqli_query($conn, "SELECT * FROM arduino"); //SQL příkaz který vrací všechny záznamy v tabulce arduino
$data = array();	//Deklarace pole data
while ($row = mysqli_fetch_object($result)) //Cyklus procházející vrácené řádky
{
    array_push($data, $row); //Ukládání řádků do pole data
}
echo json_encode($data); //Převedení do reprezentace JSON a vrácení zpět
exit();
?>