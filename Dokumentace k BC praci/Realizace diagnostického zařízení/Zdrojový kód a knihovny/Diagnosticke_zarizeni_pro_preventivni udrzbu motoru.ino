#include <SoftwareSerial.h>
#include <GPRS_Shield_Arduino.h>
#include <EmonLib.h>
#include <MPU6050.h>
#include <I2Cdev.h>
#include <OneWire.h>
#include <DallasTemperature.h>

SoftwareSerial SIM900(7, 8); 
EnergyMonitor snimacProudu;
MPU6050 snimacVibraci;
OneWire oneWireDS(6); //cislo pinu do ktereho je teplotni cidlo zapojeni
DallasTemperature teplotniSenzor(&oneWireDS);

  int16_t ax, ay, az, gx, gy, gz; //Proměnné získávané z MPU6050
  float vibrace;
  float teplota;
  float proud;
  String content; //Obsah webové stránky kde je nastavení alarmů a tel. číslo
  String alarmTel; 
  String alarmTeplota;
  String alarmProud;
  String alarmVibrace;
  String SMSmessage; 

void setup() {
   Wire.begin();
   teplotniSenzor.begin();
   snimacVibraci.initialize();
   snimacProudu.current(1, 62.6);  //Kalibrace měření proudu na základě návodu pro odpor v obvodu 35ohm
   //NASTAVENÍ BAUD RATE - JE NUTNO ZMĚNIT BAUD RATE V SÉRIOVÉM MONITORU PRO SPRÁVNÉ ZOBRAZENÍ DAT!
   Serial.begin(19200);
   SIM900.begin(19200);
   delay(30000); //Prodleva doporučená pro správnou funkcionalitu SIM900
}

void loop() {
  //SNÍMÁNÍ FYZIKÁLNÍCH VELIČIN
  teplotniSenzor.requestTemperatures();
  snimacVibraci.getMotion6(&ax, &ay, &az, &gx, &gy, &gz);
  vibrace = abs(ax);
  teplota = teplotniSenzor.getTempCByIndex(0);
  proud = snimacProudu.calcIrms(1480);
  if(proud < 0.15){proud=0;} //Vynulování mírné odchylky měření proudu

  //VYPSÁNÍ FYZIKÁLNÍCH VELIČIN NA SÉRIOVÝ MONITOR
  Serial.print("Teplota: ");
  Serial.print(teplota);
  Serial.print("°C. Proud: ");
  Serial.print(proud);
  Serial.print("[A]. Vibrace: ");
  Serial.println(vibrace);
  
  //KOMUNIKACE SE SERVEREM 
  HttpKomunikace();

  //NASTAVENÍ ALARMUJÍCÍCH HODNOT PRO SMS ZPRÁVU ZE SERVERU
  alarmTel = getValue(content,';',1);
  Serial.println(alarmTel);
  alarmTeplota = getValue(content,';',2);
  Serial.println(alarmTeplota);
  alarmProud = getValue(content,';',3);
  Serial.println(alarmProud);
  alarmVibrace = getValue(content,';',4);
  Serial.println(alarmVibrace);
  //NASTAVENÍ PODMÍNEK PRO ZASLÁNÍ SMS
  delay(1000);

  //NASTAVENÍ TEXTU SMS ZPRÁVY
    if(validateInput(alarmTeplota, teplota)){
    SMSmessage = ""; 
    SMSmessage += "Teplota presahla stanoveny limit "; 
    SMSmessage += alarmTeplota;  
    SMSmessage += " stupnu Celsia. Zaznamenana teplota: ";  
    SMSmessage += teplota; 
    SMSmessage +=" stupnu Celsia.";   
    sendSMS(SMSmessage, alarmTel);
  }
  if (validateInput(alarmProud, proud)){
    SMSmessage = "";
    SMSmessage += "Hodnota proudu presahla stanoveny limit ";
    SMSmessage += alarmProud;
    SMSmessage += "A! Zaznamenana hodnota proudu: ";
    SMSmessage += proud;
    SMSmessage +="A.";    
    sendSMS(SMSmessage, alarmTel);    
  }
  if (validateInput(alarmVibrace, vibrace)){
    SMSmessage = ""; 
    SMSmessage += "Hodnota vibraci presahla stanoveny limit "; 
    SMSmessage += alarmVibrace;  
    SMSmessage += ". Zaznamenana hodnota vibraci: ";  
    SMSmessage += vibrace; 
    SMSmessage += ".";   
    sendSMS(SMSmessage, alarmTel);  
  }
}

//Metoda validateInput slouží k porovnání dvou hodnot a kontrole veličin
bool validateInput(String input, float compareValue){
  if (isDigit(input.charAt(0)) && input.length() > 0 && compareValue > input.toFloat()){
  return true;
  }
  else{
    return false;
  }
 }

//Metoda HtttpKomunikace obsahuje několik AT příkazů které zajišťují odeslání a načtení dat z databáze serveru
void HttpKomunikace(){  

  //SEKCE ZAJIŠŤUJÍCÍ ODESLÁNÍ NASMÍNANÝCH VELIČIN DO DATABÁZE
  SIM900.println("AT+CSQ"); // Příkaz kontrolující kvalitu signálu
  delay(100);
    toSerial(); 
  toSerial();
  SIM900.println("AT+CGATT?"); // Příkaz zajišťující připojení / odpojení k GPRS službě
  delay(100);
  toSerial(); 
  SIM900.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\""); // Nastavení typu spojení na GPRS
  delay(1000);
  toSerial(); 
  SIM900.println("AT+SAPBR=3,1,\"APN\",\"CMNET\""); //Nastavení přístupového bodu APN
  delay(4000);
  toSerial();
  SIM900.println("AT+SAPBR=1,1"); //Nastavení SAPBR
  delay(2000);
  toSerial(); 
  SIM900.println("AT+HTTPINIT"); //Inicializace HTTP požadavku
  delay(2000);
  toSerial();    
  SIM900.print("AT+HTTPPARA=\"URL\",\"diagnostikamotoruarduino.cz/files/write_data.php?data1="); //Předání nasnímaných dat do odkazu
  SIM900.print(teplota);
  SIM900.print("&data2=");
  SIM900.print(proud);
  SIM900.print("&data3=");
  SIM900.print(vibrace);
  SIM900.println("\"");
  delay(1000); 
  toSerial();   

  //SEKCE ZAJIŠŤUJÍCÍ NAČTENÍ NASTAVENÍ ALARMUJÍCÍH HODNOT A TELEFONNÍ ČISLO  
  SIM900.println("AT+HTTPACTION=1"); //HTTPACTION=1 předává POST HTTP požadavek
  delay(1000);
  toSerial();   
  SIM900.println("AT+HTTPINIT"); //Inicializace HTTP požadavku 
  delay(5000);
  toSerial(); 
  SIM900.println("AT+HTTPPARA=\"URL\",\"diagnostikamotoruarduino.cz/files/getSMS.php\""); //Vložení odkazu na danou webovou stránku
  delay(1000); 
  toSerial(); 
  SIM900.println("AT+HTTPACTION=0"); //HTTPACTION=1 předává READ HTTP požadavek
  delay(10000); 
  toSerial();   
  SIM900.println("AT+HTTPREAD"); //Načtení dat z webové stránky
  delay(300);
  while(SIM900.available()!=0){  
      content = content + String(char (SIM900.read())); //Obsah webové stránky je uložen do stringu
  }
  Serial.println(content); //Vypsání obsahu webové stránky na sériový monitor
}

//Metoda sendSMS zajišťuje odeslání SMS zprávy na dané číslo v momentě kdy nasnímaná veličina překročila svůj nastavený limit
void sendSMS(String smsZprava, String telCislo) {
  Serial.println(smsZprava);
  
  SIM900.println("AT+CMGF=1\r"); //Přepnutí modulu do SMS módu
  delay(100);
  SIM900.print("AT+CMGS=\"+420"); //Zadání české předvolby
  SIM900.print(telCislo); 
  SIM900.println("\"");
  delay(3000);
  SIM900.println(smsZprava); //Vložení obsahu SMS zprávy
  delay(5000);
  SIM900.println((char)26); //Ukončení příkazu
  delay(100);
  SIM900.println();
  delay(5000); 
}

//Metoda toSerial zajišťuje vizualizaci AT příkazů a jejich odpovědi
void toSerial()
{
  while(SIM900.available()!=0)
  {
    Serial.write(SIM900.read());
  }
}

//Metoda getValue slouží k rozdělení textového řetězce na základě oddělovače, kód byl převzat z https://stackoverflow.com/questions/9072320/split-string-into-string-array
String getValue(String data, char separator, int index)
{
  int found = 0;
  int strIndex[] = {0, -1};
  int maxIndex = data.length()-1;

  for(int i=0; i<=maxIndex && found<=index; i++){
    if(data.charAt(i)==separator || i==maxIndex){
        found++;
        strIndex[0] = strIndex[1]+1;
        strIndex[1] = (i == maxIndex) ? i+1 : i;
    }
  }
  return found>index ? data.substring(strIndex[0], strIndex[1]) : "";
}
