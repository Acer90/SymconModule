[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-5.3%20%3E-green.svg)](https://www.symcon.de/forum/threads/30857-IP-Symcon-5-3-%28Stable%29-Changelog)

# SymconJSLive Gauge
Verwendet https://canvas-gauges.com/ um animierte Messinstrumente(Gauges) dazustellen.
![Animierte Gauge](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge.gif?raw=true)

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
  Mit hilfe dieses Modules können Animierte Gauges für IP-Symcon erstellt werden.

## 2. Voraussetzungen
  - IPS ab Version 5.3

## 3. Installation
   Die installation des Module ist über den Symcon Modulstore **kostenlos** möglich

## 4. Hinweise zur Verwendung

## 5. Einrichten und Aktionen
- Für die Verwendung des Moduls wird die Splitter instance benötigt, diese wird so fern noch nicht angelegt automatisch beim anlegen des Moduls mit erstellt.

**Öffne Linkn**
- Öffnet den Ausgabe link im Browser
- Der Button "Öffne Link" funktioniert nur wenn im Splitter eine Adresse gesetzt ist!

## 6. PHP-Befehlsreferenz
Keine PHP-Befehlsreferenz nötig

## 7. Parameter / Modul-Infos
GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz           | GUID                                   |
| :---------------: | :------------------------------------: |
| Device            | {71B93700-9659-97C6-AD83-984C2B44139F} |

**Allgemeine Einstellungen**

![Gauge Overview](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge-Overview.png?raw=true)

1. Titel
2. Platte (Plate)
3. Nadel/Zeiger (Needle)
4. Wert (ValueBox)
5. Fortschrittsbalken (Progressbar)
6. Schritte/Zwischenwerte (Ticks)

Weitere Infos:
https://canvas-gauges.com/documentation/user-guide/configuration#gauge-specific-configuration-options

**Expert Einstellungen**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Debug             | bool      | false         | Dienst zur erweiterten Ausgabe in Symcon
| Verwende Cache    | bool      | true          | Aktiviert den Cache-Support des Browsers
| Erstelle HTMLBox  | bool      | true          | erstellt eine Htmlbox mit Iframe zur Instance
| Vorlage Skript    | int       |               | Hier kann eine Custom Vorlage importiert werden, für weiter Informationen siehe Export von Vorlagen im Splitter Modul
| Viewport aktivieren | bool    | true          | Damit Engräte die Ausgabe der Module automatisch skalieren können
| Iframe Höhe       | int       | 0             | 0 = Auto, sonnst kann hier die höhe in Px angegeben werden.

## 8. Datenaustausch
 folgt später...

## 9. Anhang
- [canvas-gauges](https://canvas-gauges.com/)

## 10. Lizenz
  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
