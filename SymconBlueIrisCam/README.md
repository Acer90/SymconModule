# SymconBlueIrisCam

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
  Dieses Modul dient zum Abrufen und Steuern einer einzeln Kamera in BlueIris. [Blue Iris Software](https://blueirissoftware.com/)

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
  // !!!!!!!!!
  //die angabe zur den einzelnen Parametern kann der BlueIris API (https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm) entommen werden
  // !!!!!!!!!
 
  //ID der Instance BlueIrisCam
  $instanceID = 54321;
  
  //Zum Abrufen der CamConfig 
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisCam_CamConfig($instanceID, bool $reset = null, bool $enable = null, int $pause = null, bool $motion = null, bool $schedule = null, bool $ptzcycle = null, bool $ptzevents = null, int $alerts = null, int $record = null);

  //Abrufen der AlertList (einzelne Cam)
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisCam_AlertList($instanceID, int $startdate = null, bool $reset = null);
  
  //Abrufen der Clipliste (einzelne Cam)
  //die ausgabe erfolgt als JSON String!
  SymconBlueIrisCam_ClipList($instanceID, int $startdate = null, int $enddate = null, bool $tiles = null);
  
  //Zum steruen von PTZ Cameras
  //$button: this value determines the PTZ operation performed:
      //0: Pan left
      //1: Pan right
      //2: Tilt up
      //3: Tilt down
      //4: Center or home (if supported by camera)
      //5: Zoom in
      //6: Zoom out
      //8..10: Power mode, 50, 60, or outdoor
      //11..26: Brightness 0-15
      //27..33: Contrast 0-6
      //34..35: IR on, off
      //101..120: Go to preset position 1..20
  //$updown: send a value of 1 to indicate that a complementary "stop" event will follow; send 0 otherwise and the camera will be moved for a preset duration
  SymconBlueIrisCam_PTZ($instanceID, int $button = 4, int $updown = null);
  
  //löst die Aufnahme funktion aus (wie als wäre eine Bewegung erkannt worden)
  SymconBlueIrisCam_Trigger($instanceID);
  
  //erstellt ein Mediafile unterhalb der Instance (Siehe Button Modul)
  SymconBlueIrisCam_CreateMediaFile($instanceID)
```

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz |                  GUID                   |
|:-------:|:---------------------------------------:|
| Device  | {5308D185-A3D2-42D0-B6CE-E9D3080CE184}  |

Eigenschaften des 'Device' für Get/SetProperty-Befehle:  

| Eigenschaft |  Typ   | Standardwert |                                        Funktion                                         |
|:-----------:|:------:|:------------:|:---------------------------------------------------------------------------------------:|
| Short Name  | string |              | Kürzel der Kamera, über dieses wird die <br/>Instance mit der BlueIris Kamera verknüpft |
|   Use PTZ   |  bool  |    false     |     Ist dieser Eigenschaft aktiv, so werden zusätzliche Variablen für PTZ angelegt.     |
|  Show FPS   |  bool  |    false     |      Ist dieser Eigenschaft aktiv, wird zusätzlich die FPS der kamera mit ausgeben      |
|    Debug    |  bool  |    false     |                      Erweitere Debug-Ausgaben in der Debug-Konsole                      |

## 8. Datenaustausch

 weiter Information siehe Symcon BlueIris Gateway Modul

## 9. Anhang/Quelle
- [Forum](https://community.symcon.de/t/blueiris-module/44482)
- [BlueIris API Doku](https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm)
## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
