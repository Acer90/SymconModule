[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-5.3%20%3E-green.svg)](https://www.symcon.de/forum/threads/30857-IP-Symcon-5-3-%28Stable%29-Changelog)

# SymconJSLive DoughnutPie
Verwendet https://www.chartjs.org/ um Echtzeit Donat- o. Kuchendiagramme dazustellen!

![Animierte Dougtnut](https://github.com/Acer90/SymconModule/blob/alpha/imgs/DougtnutPie.gif?raw=true)

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
  Mit hilfe dieses Modules können animierte Donat- und Kuchendiagramme für IP-Symcon erstellt werden.

## 2. Voraussetzungen
  - IPS ab Version 5.3

## 3. Installation
   Die installation des Module ist über den Symcon Modulstore **kostenlos** möglich

## 4. Hinweise zur Verwendung

## 5. Einrichten und Aktionen
- Für die Verwendung des Moduls wird die Splitter instance benötigt, diese wird so fern noch nicht angelegt automatisch beim anlegen des Moduls mit erstellt.

**Öffne Link**
- Öffnet den Ausgabe link im Browser
- Der Button "Öffne Link" funktioniert nur wenn im Splitter eine Adresse gesetzt ist!

## 6. PHP-Befehlsreferenz
Keine PHP-Befehlsreferenz nötig

## 7. Parameter / Modul-Infos
GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz           | GUID                                   |
| :---------------: | :------------------------------------: |
| Device            | {9419245E-CE2E-F949-AAB6-714E2045632F} |

**Allgemeine Einstellungen**

![Gauge Overview](https://github.com/Acer90/SymconModule/blob/alpha/imgs/DougnutPie-Overview.png?raw=true)

**1. Titel**

**2. Legende**

**3. Tooltip**

**4. Drehung (Rotation)**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Start             | Int       | 180           | Winkel an den die Drehung beginnt
| Länge             | Int       | 360           | Winkel um wie weit gedreht wird

**5. Animation**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Dauer             | Int       | 500           | Dauer der Animation in ms
| Übergangsfunktion | String    | linear        | Siehe https://easings.net/

**6. Datalabels**

Weitere Infos:
https://next--chartjs-plugin-datalabels.netlify.app/

**7. Daten Einstellungen**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Genauigkeit/Präzision | Int   | 0.01 (2)      | Gibt an auf welche Nachkommastelle Werte im Diagramm gerundet werden.        

**8. Datensatz (Liste)**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Reihenfolge       | Int       | 0             | Reihenfolge in der die Datensätze geladen werden.
| Titel             | String    |               | Titel des Datensatzes (Aktuell ohne Verwendung)
| Variablen         | String    | []            | Liste siehe _Variablen (Liste in Datensätze)_
| Datalabels        | bool      | true          | Aktiviert Datalabels für den Datensatz
| Datal. Anchoring  | String    | Globale Einstsllung | Legt fest an welcher stelle die Datalabls verankert werden.
| Datal. Hintergr.  | Int       | -1            | Transparent(-1) verwendet die Globalen Einstllungen
| Datal. Hintergr. Transparenz/Alpha  | Float       | 0.50            | Wird nur berücksichtigt wenn eine Farbe gesetzt ist
| Datal. Randfarb.  | Int       | -1            | Transparent(-1) verwendet die Globalen Einstllungen
| Datal. Randfarb. Transparenz/Alpha  | Float       | 1.00            | Wird nur berücksichtigt wenn eine Farbe gesetzt ist
| Datal. Textfarb.  | Int       | -1            | Transparent(-1) verwendet die Globalen Einstllungen
| Datal. Prefix     | bool      | false         | Datalabels mit Prefix anzeigen
| Datal. Suffix     | bool      | false         | Datalabels mit suffix anzeigen

_Variablen (Liste in Datensätze)_

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Reihenfolge       | Int       | 0             | Reihenfolge in der die Datensätze geladen werden.
| Title             | String    |               | Beschriftung der Variable unter der diese In Tooltips und Legende angezeigt werden
| Variable          | Int       | 0             | **Nur Variable vom Typ Int oder Float zulässig!**
| Profile           | String    |               | Dient zum auslesen des Suffix/Prefix
| Hintergrundfarbe  | Int       | 0             |
| Hintergrundfarbe Transparenz/Alpha  | Float       | 0.50             |
| Randfarbe         | Int       | 0             |
| Randfarbe Transparenz/Alpha  | Float       | 1.00             |
| Randbreite        | Int       | 2             | Angabe in px


**Expert Einstellungen**

| Eigenschaft       | Typ       | Standardwert  | Info                                                                      |
| :---------------: | :-------: | :-----------: | :-----------------------------------------------------------------------: |
| Debug             | bool      | false         | Dienst zur erweiterten Ausgabe in Symcon
| Verwende Cache    | bool      | true          | Aktiviert den Cache-Support des Browsers
| Erstelle HTMLBox  | bool      | true          | erstellt eine Htmlbox mit Iframe zur Instance
| Vorlage Skript    | int       |               | Hier kann eine Custom Vorlage importiert werden, für weitere Informationen siehe Export von Vorlagen im Splitter Modul
| Viewport aktivieren | bool    | true          | Damit Engräte die Ausgabe der Module automatisch skalieren können
| Iframe Höhe       | int       | 0             | 0 = Auto, sonnst kann hier die höhe in Px angegeben werden.
| Benutzerdefiniertes Verhältnis | float | 0.0  | 0 = Auto, Gibt das Verhältnis von Breite zu Höhe an.

Weitere Infos:
https://www.chartjs.org/docs/latest/charts/doughnut.html

## 8. Datenaustausch
 folgt später...

## 9. Anhang
- [ChartJS](https://www.chartjs.org/)
- [Datalabels](https://next--chartjs-plugin-datalabels.netlify.app/)

## 10. Lizenz
  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
