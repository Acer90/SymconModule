# SymconBlueIrisConfigurator

Dieses Modul dient zum Erstellen der BlueIris Instancen

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
Dieses Modul dient zum Erstellen der BlueIris Instancen [Blue Iris Software](https://blueirissoftware.com/)
Werden neue Kameras in BlueIris angelegt, so können diese über dieses Modul in Symcon erstellt werden.

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
Dieses Modul sollte nach der Installation als erstes angelegt werden.
Nach dem Anlegen der Instance ist das Gateway zu konfigurieren.

## 5. Einrichten im Modul

## 6. PHP-Befehlsreferenz

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz |                  GUID                   |
|:-------:|:---------------------------------------:|
| Device  | {3B880DC9-B669-B9A1-A08B-AFE7CF6D9BD9}  |

## 8. Datenaustausch

 Siehe SymconBlueIrisGateway

## 9. Anhang/Quelle
- [Forum](https://community.symcon.de/t/blueiris-module/44482)
- [BlueIris API Doku](https://www.houselogix.com/docs/blue-iris/BlueIris/json.htm)
## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
