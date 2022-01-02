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

  Über dieses Modul können Samsung Fernseher eingeschaltet, Quellen umgeschaltet, Apps gestartet und Tasten(Keys) gesendet werden.

## 2. Voraussetzungen

  - IPS ab Version 5.0  
  - Samsung Fernseher ab 2016 der die Samsung Websocket-Api unterstützt.
 
## 3. Installation

   **ab IPS 5.0:**  
       `https://github.com/Acer90/SymconModule`  
        oder kostenlos im Modulstore unter "Samsung Tizen"

## 4. Hinweise zur Verwendung

Es werden nur Fernseher mit einer Websocket-Verbindung von Samsung unterstützt (Alle Fernseher ab 2016)
## 5. Einrichten 
    
1. Modul über den Store (Samsung Tizen) installieren
2. Im Modul müssen die folgenden Parameter definiert werden
    - IP-Adresse
        Hier ist die IP-Adresse des Fernsehers anzugeben
    - SSL verwenden
        Bei neueren Fernsehern muss diese Einstellung immer Aktiv sein.
    - Soll der Fernseher einschaltbar sein, so sind die Optionen unter Wake On Lan ebenfalls zu konfigurieren
        -> Broadcast Adresse: https://www.heise.de/netze/tools/netzwerkrechner/
        -> MAC Adresse des Fernsehers

## 6. PHP-Befehlsreferenz

<!-- language: php -->
 ```php
 <?php
  //ID der Instance (Samsung Tizen)
  $id = 54321;

  //Sendet ein Wake on Lan signal
  SamsungTizen_WakeUp($id);
  
  //Schaltet den Fernseher aus, oder sendet ein WakeOnLan Signal
  SamsungTizen_TogglePower($id);

  //senden von Tasten an den Fernseher
  //durch das verwenden des Trennzeichen ; können mehrere Tasten hintereinander gesendet werden, dafür wird das Sende-intervall verwendet
  $befehl = 'KEY_1;KEY_2;KEY_3;KEY_ENTER';
  SamsungTizen_SendKeys($id, $befehl);
  
  //aktualisiert die Variable Apps
  SamsungTizen_UpdateApps($id);
  
  //startet eine anwendung auf dem Fernseher
  //den AppName kann aus der Variable Apps entnommen werden
  $appName = "YouTube";
  SamsungTizen_StartApp($id, $appName);
  
  //Öffnet eine Webseite in der Browser-App (Startet auch den Webbrowser, wenn dieser noch nicht gestartet ist)
  $url = "https://www.symcon.de/";
  SamsungTizen_StartWebpage($id, $url);
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
## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  

## 11. Danksagung

Großen Dank geht an:
- NallChan für die unterstützung und Implementierung der SSL und Token verbindung
- Kais für die unterstützung bei der neuerstellung des WakeOnLan skripts
    