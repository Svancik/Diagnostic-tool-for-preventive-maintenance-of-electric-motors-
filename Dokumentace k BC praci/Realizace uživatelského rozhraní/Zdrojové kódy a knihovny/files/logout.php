<?php
//Tento soubor slouží k ukončení session uživatele a následnému odkázání na hlavní stránku
session_start();
unset($_SESSION["user"]);
header("Location:/index.php");
?>