[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.01-blue.svg)]()
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
[![Version](https://img.shields.io/badge/Symcon%20Version-4.3%20%3E-green.svg)](https://www.symcon.de/forum/threads/30857-IP-Symcon-4-3-%28Stable%29-Changelog)
[![StyleCI](https://styleci.io/repos/104255893/shield?style=flat)](https://styleci.io/repos/104255893)  

# SymconModule
Diese Library wurde von Acer90 erstellt, sie Enthält mehere Module für IP-Symcon ab Version 4.3 

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Software-Installation](#3-software-installation) 
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Anhang](#5-anhang)  
    1. [GUID der Module](#1-guid-der-module)
    2. [Changlog](#2-changlog)
    3. [Spenden](#3-spenden)
6. [Lizenz](#6-lizenz)

## 1. Funktionsumfang

- __SymconBlueIris__ ([Dokumentation](SymconBlueIris))  
	Überwacht ob ein bestimmtest Netzwerkgerät einen DHCP-Request sendet. (z.B. Handy betritt WLAN, Dashbutton wurde betätigt usw.)  

- __SymconSamsungTizen__ ([Dokumentation](SymconSamsungTizen))  
	Implementierung eines Clients mit Websocket Protokoll in IPS.  

- __SymconSmnp__ ([Dokumentation](SymconSmnp))  
	Implementierung eines Server mit Websocket Protokoll in IPS.  

- __SymconWS2812__ ([Dokumentation](SymconWS2812))  
	Implementierung eines Splitters für ServerSocket und WebSocket-Server.  

- __SymconWinSmnp__ ([Dokumentation](SymconWinSmnp))  
	Demo für den Datenaustausch mit dem WebSocket-Server.  

## 2. Voraussetzungen

 - IPS 4.3 oder höher  

## 3. Software-Installation

**IPS 4.3:**  
    `https://github.com/Acer90/SymconModule`

## 4. Einrichten der Instanzen in IP-Symcon

Details sind in der Dokumentation der jeweiligen Module beschrieben.  

## 5. Anhang

###  1. GUID der Module

| Modul                  | Typ      | Prefix | GUID                                   |
| :--------------------: | :------: | :----: | :------------------------------------: |
| SymconBlueIris         | Device   | BlueIris     | {7E62F9B0-5474-426F-B91B-E25F4B25A824} |
| SymconBlueIrisCam      | Device   | BlueIrisCam  | {5308D185-A3D2-42D0-B6CE-E9D3080CE184} |
| SymconSamsungTizen     | Device   | SamsungTizen | {65BF76B4-042C-4971-A5CC-292FA5E49C86} |
| SymconSmnp             | Device   | IPSSNMP      | {2F4FB7B0-AF13-46F1-9DEA-1DEBE0C3E324} |
| SymconWS2812           | Device   | SymconWS2812 | {4BF95816-240B-441A-8897-E2BDBF342207} |
| SymconWinSmnp          | Device   | IPSWINSNMP   | {1A75660D-48AE-4B89-B351-957CAEBEF22D} |

## 6. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
 
