# SymconBlueIrisSystem

Über dieses Modul werden Status Informationen von BlueIris abgerufen

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
9. [Anhang/Quelle](#9-anhang)
10. [Lizenz](#10-lizenz)

## 1. Funktionsumfang
Über dieses Modul werden Status Informationen von BlueIris abgerufen[Blue Iris Software](https://blueirissoftware.com/)

## 2. Voraussetzungen

  - IPS ab Version 5.3  
  - angelegter Benutzer (Zur Konfiguration werden Adminrechten benötigt) in Blue Iris
  - Aktivierter Webserver der vom IP-Symcon Server aus erreichbar ist
 
## 3. Installation

   1. **ab IPS 5.3:**  
       `https://github.com/Acer90/SymconModule`  
        oder kostenlos im Modulstore unter "Blue Iris"
   2. Erstellen der Konfigurator Instance
   3. Konfiguration des Gateways
   4. Erstellen der Module über den Konfigurator

## 4. Hinweise zur Verwendung
Dieses Modul sollten über den Configurator angelegt werden

## 5. Einrichten im Modul
    
- Das Interval legt fest wie oft Clips Alerts und Logs von BlueIris abgerufen werden

## 6. PHP-Befehlsreferenz

<!-- language: php -->
 ```php
 <?php
  // !!!!!!!!!
  //die angabe zur den einzelnen Parametern kann der BlueIris API (https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm) entommen werden
  // !!!!!!!!!
 
  //ID der Instance BlueIrisCam
  $instanceID = 54321;
  
  //Zum Abrufen der gibt die AlertList (für alle Kameras) aus!
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisSystem_AlertList($instanceID, int $startdate = null, bool $reset = null);
  
  //Zum Abrufen der gibt die ClipList (für alle Kameras) aus!
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisSystem_ClipList($instanceID, int $startdate = null, int $enddate = null, bool $tiles = null);
  
  //gibt den Status des BlueIrisSystems aus
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisSystem_Status($instanceID, int $signal = null, int $profil = null, string $dio = null, string $play = null);
  
  //Gibt die Logs von BlueIris aus
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisSystem_Log($instanceID);
  
  //gibt die Configuration für das Archiv und den Schedule aus
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisSystem_SysConfig($instanceID, bool $archive = null, bool $schedule = null);
  
  //Synchronisiert die AlertList-, ClipList- und LogVariable (Nur aktiv, wenn einer der 3 Eigenschaften im Modul aktiv sind!)
  //wird durch das Festgelegt Interval ausgeführt.
  SymconBlueIrisGateway_SyncData($instanceID);

```

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz |                  GUID                   |
|:-------:|:---------------------------------------:|
| Device  | {CDFC7E83-C425-E923-9B6F-ECB22F3DFCC9}  |

Eigenschaften des 'Device' für Get/SetProperty-Befehle:  

| Eigenschaft | Typ  | Standardwert |                                        Funktion                                        |
|:-----------:|:----:|:------------:|:--------------------------------------------------------------------------------------:|
|  GetClips   | bool |    false     |                           gibt die Clips als JSON-String aus                           |
|  GetAlerts  | bool |    false     |                          gibt die Alerts als JSON-String aus                           |
|   GetLog    | bool |    false     |                            gibt die Log als JSON-String aus                            |
|  Interval   | int  |      60      | Legt fest wie oft GetClips, GetAlerts und GetLog abgerufen werden sollen (in Sekunden) |

## 8. Datenaustausch

 weiter Information siehe Symcon BlueIris Gateway Modul

## 9. Anhang/Quelle
- [Forum](https://community.symcon.de/t/blueiris-module/44482)
- [BlueIris API Doku](https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm)
## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
