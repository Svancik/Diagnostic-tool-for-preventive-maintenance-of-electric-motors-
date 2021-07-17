<?php
//Tento soubor otevírá GSM modul Arduina SIM900 pro zápis dat. Probíhá načtení dat z odkazu které jsou posléze vložena do tabulky arduino. 
  include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi
     $sql = "INSERT INTO arduino (teplota, proud, vibrace) VALUES ('".$_GET["data1"]."', '".$_GET["data2"]."', '".$_GET["data3"]."')";   //SQL příkaz načte data která jsou předáná v odkazu např. write_data.php?data1=100&data2=200&data3=300 do daných sloupců
    mysqli_query($conn,$sql); //Inicializace query
    echo"success";
?>