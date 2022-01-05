# SymconBlueIrisGateway

Dieses Modul dient zum Abrufen und Steuern einer einzeln Kamera in BlueIris.

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
Dieses Modul dient zum Abrufen und Steuern der [Blue Iris Software](https://blueirissoftware.com/)

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
Dieses Modul wird durch den Konfigurator automatisch angelegt

## 5. Einrichten im Modul
    
1. Das Kürzel der Shortname wird in BlueIris definiert, dieser Dient auch diesen Modul zur verknüpfung mit der richtigen Kamera

## 6. PHP-Befehlsreferenz

<!-- language: php -->
 ```php
 <?php
  // !!!!!!!!!
  //die angabe zur den einzelnen Parametern kann der BlueIris API (https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm) entommen werden
  // !!!!!!!!!
 
  //ID der Instance BlueIrisCam
  $instanceID = 54321;
  
  //gibt die sessionId nach den Loginwert zurück
  $sessionID = SymconBlueIrisGateway_Login($instanceID);
  
  //Über diese Funktion, kann selbständig Daten an den BlueIris Webserver gesendet werden.
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisGateway_SendJSONData($instanceID, string $cmd, string $json_string);
  
  //gibt alle Cams mit Information aus
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisGateway_CamList($instanceID, $sessionID);
  
  //Synchronisiert die BlueIris-Daten mit den Child-Instancen
  //wird durch das Festgelegt Interval ausgeführt.
  SymconBlueIrisGateway_SyncData($instanceID);
  
  //logt die Benutzer(SessionID) wieder aus
  SymconBlueIrisGateway_Logout($instanceID, $sessionID);
```

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz |                  GUID                   |
|:-------:|:---------------------------------------:|
| Device  | {E138AFDC-D1E0-B462-A5E5-AF24F57D4686}  |

Eigenschaften des 'Device' für Get/SetProperty-Befehle:  

| Eigenschaft |  Typ   | Standardwert  |                                                      Funktion                                                       |
|:-----------:|:------:|:-------------:|:-------------------------------------------------------------------------------------------------------------------:|
|  IPAddress  | string | 192.168.178.1 |                                         IP-Adresse des BlueIris Webservers                                          |
|    Port     |  int   |      81       |                                            Port des BlueIris Webservers                                             |
|   Timeout   |  int   |       3       |                              Zeit ab wann eine Abfrage abgebrochen wird (in Sekunden)                               |
|  Username   | string |     admin     | Benutzername für das BlueIris Webinterface<br/>Für die konfiguration über die Webapi sind Adminrechte erforderlich! |
|  Password   | string |               |                                       Passwort für den Webinterface-Benutzer                                        |
|  Interval   |  int   |      10       |                                           Abfrage interval (in Sekunden)                                            |
|    Debug    |  bool  |     false     |                                    Erweitere Debug-Ausgaben in der Debug-Konsole                                    |

## 8. Datenaustausch

 weiter Information folgen später

## 9. Anhang/Quelle
- [Forum](https://community.symcon.de/t/blueiris-module/44482)
- [BlueIris API Doku](https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm)
## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
