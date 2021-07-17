 <?php
include 'dbh.php'; //Zahrnutí souboru obsahující údaje nutné k napojení na databázi

if (isset($_POST["oldRows"]) && isset($_POST["newRows"])) { //Kontrola zda byl předán počet nových a starých řádků
	$newArray = array(); //Vytvoření pole
	$oldRows = $_POST["oldRows"]; //Uložení počtu starých řádků do lokální proměnné
	$newRows = $_POST["newRows"]; //Uložení počtu nových řádků do lokální proměnné
	
			$sql = "SELECT * FROM arduino WHERE id Between ".$oldRows." and ".$newRows.""; //SQL příkaz který selektuje pouze nové řádky (od počtu starých po počet nových)
			$result = mysqli_query($conn, $sql);	//Inicializace SQL příkazu
			while($row = mysqli_fetch_assoc($result)){ //Cyklus procházející řádky
				array_push($newArray, $row); //Vložení nového řádku do pole newArray
			}
			echo json_encode($newArray); //Převedení do reprezentace JSON a vrácení zpět


}	
else {
	echo json_encode(0); //Nic nevrací pokud není oldRows a newRows
}
 exit();
?>
 
