# SymconSnmp

Implementierung eines Snmp Clientes in IP-Symcon

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Hinweise zur Verwendung](#4-hinweise-zur-verwendung)
5. [Einrichten](#5-einrichten)
6. [PHP-Befehlsreferenz](#6-php-befehlsreferenz)
7. [Parameter / Modul-Infos](#7-parameter--modul-infos)
8. [Datenaustausch](#8-datenaustausch)
9. [Anhang](#9-anhang)
10. [Lizenz](#10-lizenz)

## 1. Funktionsumfang

  Dieses Modul ermöglicht es einen Steca Solarix PLI 5000-48 Wechselrichter auszulesen

## 2. Voraussetzungen

  - IPS ab Version 4.3  
 
## 3. Installation

   **ab IPS 4.3:**  
       `https://github.com/Acer90/SymconModule` 
       
## 4. Hinweise zur Verwendung
Infos über die einzeln Variablen findest du im Handbuch des Wechselrichters

[https://www.steca.com/](https://www.steca.com/frontend/standard/popup_download.php?datei=210/21080_0x0x0x0x0_Steca_Solarix_PLI_Manual_DE.pdf)  

## 5. Einrichten 

- Einen Serial Port (I/O Instance) hinzufügen
- Folgende Daten eintragen:
    
| Eigenschaft      | Wert    | Info                                                                    |
| :--------------: | :-----: |:-----------------------------------------------------------------------:|
| Port             | COM1    | Hier den Commport auswählen an den der Wechselrichter angeschlossen ist |
| Baudrate         | 2400    |                                                                         |
| Datenbits        | 8       |                                                                         |
| Stopbits         | 1       |                                                                         |
| Parität          | Kein(e) |                                                                         |
    
- Module Steca Solaric (Instance) erstellen
- Gateway auf die grade angelegte Serial Port I/O ändern
- Einstellen der Abrufzeiten (in Sekunden)
- Speichern
      
## 6. PHP-Befehlsreferenz

<!-- language: php -->
 ```php
 <?php
  StecaSolarix_Load_LiveData(59067 /*[StecaSolarix]*/);
  //Aktualisiert alle Status-Variablen
  
  StecaSolarix_Load_LiveProperties(59067 /*[StecaSolarix]*/);
  //Aktualisiert alle Parameter-Variablen

```

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz          | GUID                                   |
| :--------------: | :------------------------------------: |
| Device  | {DCD58674-94D6-F15E-9537-F1627F145417} |

Parameter der Instance:

| Eigenschaft                | Typ     | Standardwert | Funktion                                                               |
| :------------------------: | :-----: | :----------: | :--------------------------------------------------------------------: |
| Abruf Live-Daten           | int     | 2            | Intervall in der die Live-Daten aktualisiert werden (Sekunden)         |
| Abruf Geräteeigenschaften  | int     | 600          | Intervall in der die Geräteeigenschaften aktualisiert werden (Sekunden)|

## 8. Datenaustausch

 (Kein Datenaustausch möglich)

## 9. Anhang

## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
