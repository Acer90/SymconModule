[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Modul%20Version-3.23-blue.svg)]()
[![Version](https://img.shields.io/badge/Symcon%20Version-5.3%20%3E-green.svg)](https://www.symcon.de/forum/threads/30857-IP-Symcon-5-3-%28Stable%29-Changelog)
# SymconJSLive Splitter

Splitter für alle JSLive Module

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
  Dieses Modul dient als Schnittstelle zwischen den JSLive Modulen und der Webhook Instance

## 2. Voraussetzungen
  - IPS ab Version 5.3

## 3. Installation
   Die installation des Module ist über den Symcon Modulstore **kostenlos** möglich

## 4. Hinweise zur Verwendung

###Daten-Modus
####Websocket (Empfohlen)
- Bei diesen Modus baut der Client über den Webhook und Splitter eine Verbindung zum jeweiligen Modul auf. 
Dabei werden nur Daten bei Veränderung von werden an den Client mitgeteilt.

####Interval
- Ist Interval aktiv, fragt der Client über die API des JSLive Moduls im unter "Abfrage-Zeit" angegebenen Interval ab.
Achtung dieser Modus sorgt für einen erhöhten Datentransfer, und belastet bei Verwendung den Connect-Dienst.

## 5. Einrichten und Aktionen

der JSLive-Splitter kann auch ohne konfiguration verwendet werden, aus Sicherheitsgründen sollte aber ein Password
gesetzt werden. Dieses verhinder vor unbefugten zugriff Dritter!

**Connect Adresse laden**
  - Lädt die Addresse vom Connect Modul

**Benutzerdefiniert Vorlagen laden**
  - Dient zum exportieren der Vorlagen, diese können im Anschluss angepasst und einzelnen Modulen unter 
    Expert => Vorlagen-Skript zugewiesen werden.
  - Zum exportieren ist die angabe einer Kategorie nötig(Die Stammkategorie ist nicht zulässig!)

## 6. PHP-Befehlsreferenz

Keine PHP-Befehlsreferenz nötig

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz           | GUID                                   |
| :---------------: | :------------------------------------: |
| Splitter          | {9FFF3FC0-FD51-C289-FA36-BC1C370946CF} |

**Allgemeine Einstellungen**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Passwort          | string    |               | Password für den Webhook-Zugriff
| Adresse           | string    | http://127.0.0.1:3777 | Webadresse des IP-Symcon Servers
| Daten Modus       | string    | Websocket     | Beschreibung unter 4. [Hinweise zur Verwendung](#4-hinweise-zur-verwendung)
| Interval Abfrage-Zeit | int       | 3             | in Sekunden

**Viewport Eisntellungen**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Aktivieren        | bool      | true          | Damit Engräte die Ausgabe der Module automatisch skalieren können
| Content           | string    | width=device-width, initial-scale=1, maximum-scale=1.0, minimum-scale=1, user-scalable=no | Html-Parameter der Viewport Configuratzion

**Expert Eisntellungen**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Debug             | bool      | false         | Dienst zur erweiterten Ausgabe in Symcon
| Verwende Cache    | bool      | true          | Aktiviert den Cache-Support des Browsers
| Verwende Kompression | bool   | true          | Aktiviert die GZip Kompression und verringert den Datenverbrauch
| Frame benutzt den vollen Link als Quelle | bool | false | Bei Aktivierung werden alle Iframes auf einen Absoluten Pfad umgestellt.(**Bei Verwendung von Aio-Neo nötig!**)


## 8. Datenaustausch

 folgt später...

## 9. Anhang

## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
