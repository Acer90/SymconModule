# SymconSamsungTizen

Dieses Modul dient zur Steuerung von Samsung Tizen Fernsehern(ab 2016)

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
11. [Danksagung](#11-Danksagung)

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
Dieses Modul sollten über den Configurator angelegt werden

## 5. Einrichten im Modul
    
1. Das Kürzel der Shortname wird in BlueIris definiert, dieser Dient auch diesen Modul zur verknüpfung mit der richtigen Kamera

## 6. PHP-Befehlsreferenz

<!-- language: php -->
 ```php
 <?php
  //ID der Instance (Samsung Tizen)
  $id = 54321;

  //die angabe zur den einzelnen Parametern kann der 
  public function CamConfig(bool $reset = null, bool $enable = null, int $pause = null, bool $motion = null, bool $schedule = null, bool $ptzcycle = null, bool $ptzevents = null, int $alerts = null, int $record = null);
```

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz          | GUID                                   |
| :--------------: | :------------------------------------: |
| Device  | {65BF76B4-042C-4971-A5CC-292FA5E49C86} |

Eigenschaften des 'Device' für Get/SetProperty-Befehle:  

|   Eigenschaft    |  Typ   |   Standardwert    |                                Funktion                                |
|:----------------:|:------:|:-----------------:|:----------------------------------------------------------------------:|
|    IPAddress     | string |   192.168.178.1   |                     Die IP-Adresse des Fernsehers                      |
|    MACAddress    |  int   | aa:bb:cc:00:11:22 |                     Die MAC Adresse des Fernsehers                     |
| BroadcastAddress | string |                   |  Die Broadcast-Adresse des Fernsehers ( [siehe oben](#5-einrichten) )  |
|     Interval     |  int   |        10         | Zum Prüfen, ob der Fernseher wieder eingeschaltet wurde. (In Sekunden) |
|      Sleep       |  int   |       1000        |                    Sendeintervall in Millisekunden                     |
|      UseSSL      |  bool  |       true        |               Verwendet SSL bei der Websocket-Verbindung               |

## 8. Datenaustausch

 (Kein Datenaustausch möglich)

## 9. Anhang
- [Forum](https://community.symcon.de/t/hilfe-bei-websocket-client-fuer-samsung-tizen-fernseher-gesucht/44532)
- [BlueIris API Doku](https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm)
## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  

## 11. Danksagung

Großen Dank geht an:
- NallChan für die unterstützung und Implementierung der SSL und Token verbindung
- Kais für die unterstützung bei der neuerstellung des WakeOnLan skripts
