<?php
include 'files/dbh.php'; //Soubor zajišťující napojení na databázi
include 'files/userSession.php'; //Soubor zajišťující přihlašovací relaci
?>
<!DOCTYPE html>
<html>

<head>
<meta content="width=device-width, initial-scale=1" name="viewport" />
<!--Načtení knihovny PLOTLY zajišťující vizualizaci dat v trendech-->    
	<script src="files/plotly.min.js"></script>   
<!--Načtení knihovny jQuery zajišťující AJAX, Cookies a práci s DOM-->    
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--Načtení knihovny d3-time-format zajišťující zpřístupnění časových údajů--> 	
    <script src="https://d3js.org/d3-time-format.v3.min.js"></script>
<!--Načtení přednastavené sady kaskádových stylů bootstrap--> 	    
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<!--Načtení vlastní sady kaskádových stylů z externího souboru--> 	
	<link rel="stylesheet" href="files/mystyle.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head> 

<body>

<!--Odhlašovací tlačítko-->
<div class = "header">	
	<form action ='files/logout.php'>
		<button id="signout" type ="submit">Odhlásit se</button> 
	</form> 
</div>

<!--Kontejner-->
<div class="wrapper">
	<!--Třetina kontejneru-->
  <div class="third">
    <h4> Trend teploty motoru </h4>
    <!--Graf s daty je doplněn přes JS-->
    <div id="chart1"></div>    
    <!--Tabulka s daty je doplněna přes JS--> 
    <div class ="events">		
			<div class ="headerTable">
				<span id="numAlarms1"> </span> <span> alarmů teploty motoru  </span>
				<!--Změna alarmu--> 
				<button onclick="changeAlarm(teplota, 'cookTeplota')" class="alarmButton" type ="button"/>&#9881;
				<span class="setAlarmText">(změna alarmu)</span>
			</div>
			<div class="eventstable">
			  	<table class="table table table-sm" style="">
					<thead class="thead-dark">				
					<th>Datum</th> <th> Čas </th> <th> Událost </th> <th> Hodnota  </th>				
					</thead>
					<tbody id="table1">
						<!--Obsah doplněn přes JS-->
					</tbody>
				</table>		
			</div>        											 	 			
		</div>  	
  	</div>

    <!--Třetina kontejneru-->
  	<div class="third">
    <h4> Trend proudu motoru </h4>
    <!--Graf s daty je doplněn přes JS-->
    <div id="chart2"></div> 
    <!--Tabulka s daty je doplněna přes JS-->    
    <div class ="events">		
			<div class ="headerTable">
				<span id="numAlarms2"> </span> <span> alarmů proudu motoru  </span>
				<!--Změna alarmu--> 
				<button onclick="changeAlarm(proud, 'cookProud')" class="alarmButton" type ="button"/>&#9881;
				<span class="setAlarmText">(změna alarmu)</span>
			</div>
			<div class="eventstable">
			  	<table class="table table table-sm" style="">
					<thead class="thead-dark">				
					<th>Datum</th> <th> Čas </th> <th> Událost </th> <th> Hodnota  </th>				
					</thead>
					<tbody id="table2">
						<!--Obsah doplněn přes JS-->
					</tbody>
				</table>		
			</div>        											 	 			
		</div>  
  	</div>

  	<!--Třetina kontejneru-->
  	<div class="third">
    <h4> Trend vibrací motoru </h4>
    <!--Graf s daty je doplněn přes JS-->
    <div id="chart3"></div>   
    <!--Tabulka s daty je doplněna přes JS-->  
    <div class ="events">		
			<div class ="headerTable">
				<span id="numAlarms3"> </span> <span> alarmů vibrací motoru  </span>
				<!--Změna alarmu--> 
				<button onclick="changeAlarm(vibrace, 'cookVibrace')" class="alarmButton" type ="button"/>&#9881;
				<span class="setAlarmText">(změna alarmu)</span>
			</div>
			<div class="eventstable">
			  	<table class="table table table-sm" style="">
					<thead class="thead-dark">				
					<th>Datum</th> <th> Čas </th> <th> Událost </th> <th> Hodnota  </th>				
					</thead>
					<tbody id="table3">
						<!--Obsah doplněn přes JS-->
					</tbody>
				</table>		
			</div>        											 	 			
		</div>  
  	</div>

</div>

<br>
<br>

<!--Formulář na konfiguraci SMS zpráv v případě překročení alarmující hodnoty-->
<div class="smsNotification">
	<h2>SMS notifikace alarmů &#128241;</h2>	
	<div class="form" id="setSMSform">
		<form action="/files/setSMS.php" method="POST" class="form-container">  
			<label for="phoneNumber">Telefonní číslo (+420)</label><input type="number" id ="SMStel" name="tel" required placeholder="Zadejte tel. číslo" >	<br>
			<label for="teplota" step=".01" >Maximální teplota</label><input type="number" id="SMSteplota"name="Ateplota" placeholder="Není definováno"   >	<br>
			<label for="teplota">Maximální odběr proudu</label><input type="number" step=".01"  id = "SMSproud" name="Aproud" placeholder="Není definováno"  ><br>
			<label for="teplota" step=".01">Maximální hodnota vibrací</label><input type="number" id="SMSvibrace" name="Avibrace" placeholder="Není definováno" ><br>
			<button type="submit" class="btn" id="btn_set" >Změnit nastavení</button>			   	
		</div> 
	</div>
</div>

</body>
</html> 

<script>
//Vytvoření vlastního objektu grafu s proměnnými které udávají vlastnosti grafu
class Chart{
	constructor(valueColor, alarmValue, alarmValueColor, xAxisTitle, yAxisTitle, traceName, alarmName, unitValue, htmlChartID, htmlAlarmID, htmlTableID, eventText, htmlNumAlarmsID, countAlarm){
	this.value = [];	//Hodnota grafu
	this.valueColor = valueColor; //Barva hodnoty grafu
	this.alarmValue = alarmValue; //Hodnota alarmu
	this.alarmValueColor = alarmValueColor; //Barva hodnoty alarmu
	this.traceName = traceName; //Název osy 
	this.alarmName = alarmName; //Název alarmu
	this.unitValue = unitValue; //Jednotka hodnoty
	this.htmlChartID = htmlChartID; //HTML ID elementu grafu
	this.htmlAlarmID = htmlAlarmID; //HTML ID elementu alarmu
	this.htmlTableID = htmlTableID; //HTML ID elementu tabulky
	this.htmlNumAlarmsID = htmlNumAlarmsID; //HTML ID elementu počtu alarmů
	this.eventText = eventText; //Text události
	this.countAlarm =countAlarm; //Počet alarmů
	this.layoutChart = { //Definice rozlošení agrafu
						 autosize: true,  
						 margin: {l: 50, r: 5, b: 40, t: 15, pad: 2}, 
						 xaxis:{title: { text: this.xAxisTitle}}, 
						 name:traceName, 
						 yaxis:{title: { text: this.yAxisTitle}}, 
						 paper_bgcolor:'rgba(0,0,0,0)', 
						 plot_bgcolor:'rgba(0,0,0,0.5)', 
						 textfont_color:'rgba(1,1,1,1)', 
						 x: this.xAxisTitle, y: this.yAxisTitle, 
						font: { color: valueColor }};
	}
	//Vytvoření metody createChart() pro objekt Chart na základě již vytvořené metody drawChat()
	createChart(){
		drawChart(timestamp, this.value, this.htmlChartID, this.valueColor, this.layoutChart, this.traceName, this.unitValue);
	}
	//Vytvoření metody visualizeAlarm() pro objekt Chart na základě již vytvořené metody visualizeAlarmValue()
	visualizeAlarm(){
		visualizeAlarmValue(this.alarmValue, this.htmlChartID, this.htmlAlarmID, timestamp, this.alarmValueColor, this.alarmName, this.unitValue);
	}
	//Vytvoření metody registerEvents() pro objekt Chart na základě již vytvořené metody registerEvents()
	registerEvents(startingArrayIndex){
		registerEvents(startingArrayIndex,this.alarmValue, this.value, this.unitValue, this.htmlTableID, this.eventText, this.valueColor, this.htmlNumAlarmsID, this.countAlarm);		
	}
}

var timestamp = []; //Časové razítko které je zobrazeno na ose x grafu
var splitTimestamp; //Proměnná pro rozdělení časového razítka do jednotlivých časových údajů
var sampling = 5000; //Proměnná uchovávajcí čas kontroly nových záznamů v milisekundách

var teplota = new Chart('#F4DE29',getCookie("cookTeplota"),'#FF0000','Čas','Teplota',"Teplota ","Alarm"," °C","chart1","setAlarm1","table1", "Teplota motoru překročila alarm - ", "numAlarms1", 0); //Graf teploty získávající hodnoty z databáze z DS18B20
var proud = new Chart('#7CCFDC',getCookie("cookProud"),'#FF0000','Čas','Proud',"Proud ","Alarm"," A","chart2","setAlarm2","table2", "Odběr proudu překročil alarm - ", "numAlarms2", 0); //Graf teploty získávající hodnoty z databáze z SCT-013
var vibrace = new Chart('#29F4B0',getCookie("cookVibrace"),'#FF0000','Čas','Vibrace',"Vibrace ","Alarm","","chart3","setAlarm3","table3", "Vibrace překročily alarm - ", "numAlarms3", 0); //Graf vibrací získávající hodnoty z databáze z MPU-6050
var charts = [teplota, proud, vibrace]; //Vložení 3 grafů do pole

initialize();  //Provede se při načtení stránky 

function initialize(){  //Inicializace během které se načtou veškerá aktuální data z databáze a jsou přidána do proměnných které jsou vizualizovány do jednotlivých grafů.
    	setPlaceholderForm(); //Tato metoda má na starotsti předvyplnění formuláře na stránce
$.get("files/initialization.php", {	 //Získání dat z databáze pomocí AJAX
		}, function(data, status){				 
				data = JSON.parse(data);	//Data jsou parsovány do podoby JSON
				for(var a = 0; a < data.length; a++)  //Cyklus procházející všechna 
	            { 	     teplota.value.push(data[a].teplota); 					//Přidání dat do proměnných 
			             proud.value.push(data[a].proud); 						//Přidání dat do proměnných 
			             vibrace.value.push(data[a].vibrace); 					//Přidání dat do proměnných     
			             timestamp.push(timestampToDate(data[a].timestamp));	//Přidání dat do proměnných 		               
			    }         
			   
			    for (var i = 0; i < charts.length; i++) { //Cyklus procházející všechny grafy
			    	charts[i].createChart(); //Metoda vytvoří graf
			    	charts[i].visualizeAlarm(); //Metoda vizualizuje alarm
			    	charts[i].registerEvents(0); //Metody vytvoří tabulku událostí			   
			    }	
				setInterval(function(){
			    	 checkAndAddNewRecords(); //Kontrola zda je v databázi nový řádek
			    },sampling);	//Je to prováděno v časovém intervalu [ms] na základě hodnoty v proměnné sampling
		});
}		

function setPlaceholderForm() {	 //Tato metoda přednastaví text ve formuláři na základě posledního řádku v databázi
$.get("files/getFormSettings.php", {	//Získání dat z databáze pomocí AJAX		
		}, function(data, status){		
				data = JSON.parse(data);
			   	document.getElementById("SMStel").value=data[0].tel; 			//Vybrání HTML elementu na základě id a doplnění dat 
				document.getElementById("SMSteplota").value=data[0].Ateplota; 	//Vybrání HTML elementu na základě id a doplnění dat 
				document.getElementById("SMSproud").value=data[0].Aproud; 		//Vybrání HTML elementu na základě id a doplnění dat 
				document.getElementById("SMSvibrace").value=data[0].Avibrace; 	//Vybrání HTML elementu na základě id a doplnění dat 
				});		
}

function timestampToDate(stringTimestamp){ 		//Tato metodá převede časové razítko do rozpoznatelného formátu pro uložení do Date.
var splitTimestamp = stringTimestamp.split(/[- :]/);
var dateFormat = new Date(splitTimestamp[0], splitTimestamp[1], splitTimestamp[2], '0'+splitTimestamp[3].slice(-2),'0'+splitTimestamp[4].slice(-2), '0'+splitTimestamp[5].slice(-2));
return dateFormat;
}

function drawChart(xAxisData, yAxisData, htmlChartID, color, layout, traceName, unit){ //Metoda vykreslující graf
	Plotly.newPlot(htmlChartID,[{ x:xAxisData, y:yAxisData, type:'line', name:traceName + yAxisData[yAxisData.length-1] + unit, line: {'color' : color}}], layout);
}

function visualizeAlarmValue(alarmValue, htmlChartID, htmlTextID, xAxisData, color, traceName, unit){ //Metoda vládacící alarmující hodnotu do grafu
	var xAxisAlarmValue = []; //Na ose X bude rovinná čára zobrazující limit
	var yAxisAlarmValue = []; //Na ose Y bude alarmující hodnota alarmValue
	for (var i = 0; i < xAxisData.length; i++) { //Je procházené vešekré pole hodnot
		xAxisAlarmValue.push(i);
		yAxisAlarmValue.push(alarmValue);
	}
	Plotly.addTraces(htmlChartID, {x: xAxisData,y: yAxisAlarmValue, line: {'color' : color},name:traceName+": "+alarmValue +unit}); //Přidání alarmu do grafu
}

function registerEvents(startingArrayIndex, alarmValue, yAxisData, unit, tableID, eventText, valueColor, htmlNumAlarmsID, countAlarm){ //Tato metoda přidává data do tabulky událostí
var Parent = document.getElementById(tableID); //Nastavení HTML elemtnu jako rodičovský element
	while(Parent.hasChildNodes()) //ZDROJOVÝ KÓD PŘEVZATÝ Z https://www.daniweb.com/programming/web-development/threads/113340/delete-all-rows-from-table-in-javascript
	{
	   Parent.removeChild(Parent.firstChild); //Odstranění dětí rodičovského elementu
	}
	var table = document.getElementById(tableID); 
	for (var i = startingArrayIndex; i < yAxisData.length; i++) { //Nastavení cyklu aby procházel pole která začínající od čísla předané do metody až po délku pole
		if (parseFloat(yAxisData[i]) > parseFloat(alarmValue)){ //Pokud je hodnota pole vyšší než hodnota alarmující hodnoty tak se vygeneruje nový řádek v tabulce událostí
				countAlarm++; //Počet všech alarmů se zvedne
				var row = table.insertRow(0); //Vyvoření řádku	
  				var cell1 = row.insertCell(0); //Vyvoření sloupce / buňky
  				var cell2 = row.insertCell(1); //Vyvoření sloupce / buňky
  				var cell3 = row.insertCell(2); //Vyvoření sloupce / buňky
  				var cell4 = row.insertCell(3); //Vyvoření sloupce / buňky
				 cell1.innerHTML = "<i>"+getFormatedDate(timestamp[i])+"</i>"; 		//Vložení datumu do prvního sloupce / buňky
				 cell2.innerHTML = "<i>"+getFormatedTime(timestamp[i])+"</i>"; 		//Vložení času do druhého sloupce / buňky	  	
  				 cell3.innerHTML = eventText+"<span style='color: "+valueColor+"'>"+alarmValue+"</span>";+" "+unit; 		//Vložení textu události do třetího sloupce / buňky
  				 cell4.innerHTML = "<b style='color: "+valueColor+"'>"+yAxisData[i]+"</b>";		//Vložení hodnoty pole do čtvrtého sloupce / buňky
			}	
	}
		document.getElementById(htmlNumAlarmsID).innerHTML=countAlarm; //Přepsání textu zobrazující počet všech alarmů
}	

function getFormatedDate(timestamp){ //Metoda vrací datum z timestampu
var formatedTimestamp = ('0' + timestamp.getDate()).slice(-2) + '.'+ ('0' + (timestamp.getMonth()+1)).slice(-2) + '.'+ timestamp.getFullYear();
return formatedTimestamp;
}

function getFormatedTime(timestamp){ //Metoda vrací čas z timestampu
var formatedTimestamp = ('0' + timestamp.getHours()).slice(-2) + ':'+ ('0' + (timestamp.getMinutes())).slice(-2) + ':'+ ('0' + (timestamp.getSeconds())).slice(-2);
return formatedTimestamp;
}

function checkAndAddNewRecords() { //Tato metoda porovnává zda je v databázi nový záznamy
$.get("files/check_rows.php", {	 //Získání dat z databáze pomocí AJAX			 
		}, function(data, status){				 
				data = JSON.parse(data);					  	
				if (data > timestamp.length){ //Pokud je počet dat načtených z databáze vyšší než délka současného pole tak se načtou nová data
				loadNewData(data);  //Předání dat z databáze volané metodě loadNewData() 
       		  	}
       		  	else{
       		  	console.log("V databázi nejsou žádné nové záznamy.");	
       		  	}    			
		});
}

function loadNewData(newRows){
	 $.post("files/load_data.php", { //Získání dat z databáze a předání dat na PHP stránku pomocí AJAX	
 			oldRows : timestamp.length, //Předání délky pole starých záznamů, zde může být libovolné pole, já zvolil timestamp
			newRows : newRows,   //Předání délky pole nových záznamů
		}, function(data, status){				 
				 data = JSON.parse(data);	//Data jsou parsovány do podoby JSON				
				for(var a = 1; a < data.length; a++) 		//Jsou procházena veškerá data z load_data.php
	            {
			       teplota.value.push(data[a].teplota);								//Přidání hodnot do proměnné
			       proud.value.push(data[a].proud);									//Přidání hodnot do proměnné
			       vibrace.value.push(data[a].vibrace);								//Přidání hodnot do proměnné	
			       timestamp.push(timestampToDate(data[a].timestamp));	
			    }
				for (var i = 0; i < charts.length; i++) {  //Jsou procházeny všechny grafy a opakují se kroky jako během inicializace
			    	charts[i].createChart();	
			    	charts[i].visualizeAlarm();
			    	charts[i].registerEvents(0);
			    }			
		});
}
		
function changeAlarm(ChartID, cookieVariable){	//Tato metoda mění hodnotu alarmu a s tím spojené operace
	input = prompt("Zadejte novou číselnou hodnotu alarmu", ChartID.alarmValue);  //Vyskočí okno do kterého je třeba vylnit číselnou hodnotu alarmu
	 if ((input != null || input != "") && !isNaN(input)) { //Ověření zda se jedn o neprázdný záznam a zda je to tektový řetězec
   		ChartID.alarmValue = input;							//Změna proměnné grafu
	 	Plotly.deleteTraces(ChartID.htmlChartID,1); 		//Odstranění staré alarmující čáry
	 	ChartID.visualizeAlarm();							//Vykreslení nové alarmující čáry
	 	ChartID.registerEvents(0);							//Uložení nové alarmující hodnoty do Cookie
	 		setCookie(cookieVariable, input, 30);
	 }
	 else{	 	
		alert("Zadejte pouze číselnou hodnotu!");	 	
	 }  
}

//ZDROJOVÝ KÓD 2 METOD NÍŽE BYL PŘEVZAT Z https://www.w3schools.com/js/js_cookies.asp
function setCookie(cname,cvalue,exdays) { //Nastavení Cookie
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires=" + d.toGMTString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {	//Získání Cookie
	  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
}

</script>