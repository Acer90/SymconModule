[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.01-blue.svg)]()
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
[![Version](https://img.shields.io/badge/Symcon%20Version-4.3%20%3E-green.svg)](https://www.symcon.de/forum/threads/30857-IP-Symcon-4-3-%28Stable%29-Changelog)
[![StyleCI](https://styleci.io/repos/104255893/shield?style=flat)](https://styleci.io/repos/104255893)  

# IPSNetwork
Diese Library enthält verschiedene Module für Netzwerkanwendungen.  

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

- __DHCP-Sniffer__ ([Dokumentation](DHCPSniffer))  
	Überwacht ob ein bestimmtest Netzwerkgerät einen DHCP-Request sendet. (z.B. Handy betritt WLAN, Dashbutton wurde betätigt usw.)  

- __WebSocket-Client__ ([Dokumentation](WebSocketClient))  
	Implementierung eines Clients mit Websocket Protokoll in IPS.  

- __WebSocket-Server__ ([Dokumentation](WebSocketServer))  
	Implementierung eines Server mit Websocket Protokoll in IPS.  

- __Client-Splitter__ ([Dokumentation](ClientSplitter))  
	Implementierung eines Splitters für ServerSocket und WebSocket-Server.  

- __WebSocket-Server Demo-Modul__ ([Dokumentation](WebSocketServerIfTest))  
	Demo für den Datenaustausch mit dem WebSocket-Server.  

## 2. Voraussetzungen

 - IPS 4.3 oder höher  

## 3. Software-Installation

**IPS 4.3:**  
   Bei privater Nutzung: Über das 'Module-Control' in IPS folgende URL hinzufügen.  
    `git://github.com/Nall-chan/IPSNetwork.git`  

   **Bei kommerzieller Nutzung (z.B. als Errichter oder Integrator) wenden Sie sich bitte an den Autor.**  

## 4. Einrichten der Instanzen in IP-Symcon

Details sind in der Dokumentation der jeweiligen Module beschrieben.  

## 5. Anhang

###  1. GUID der Module

| Modul                  | Typ      | Prefix | GUID                                   |
| :--------------------: | :------: | :----: | :------------------------------------: |
| Client Splitter        | Splitter | WSC    | {7A107D38-75ED-47CB-83F9-F41228CAEEFA} |
| DHCP Sniffer           | Device   | DHCP   | {E93BCE5E-BA95-424E-8C3A-BF6AEE6CB976} |
| WebsocketClient        | Splitter | WSC    | {3AB77A94-3467-4E66-8A73-840B4AD89582} |
| WebsocketServer        | Splitter | WSS    | {7869923C-6E1D-4E66-A0BD-627FAD1679C2} |
| WebSocketInterfaceTest | Splitter | WSTest | {FC11DB7C-4999-4EA7-B57A-82A878ADD273} |

### 2. Changlog

Version 2.01:  
 - Doku ergänzt  

Version 2.0:  
 - WebSocket-Module in IPSNetwork überführt  

Version 1.0:  
 - Erstes offizielles Release  

### 3. Spenden  
  
  Die Library ist für die nicht kommzerielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:  

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=G2SLW2MEMQZH2" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>

## 6. Lizenz

  IPS-Modul:  
  [CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)  
 
