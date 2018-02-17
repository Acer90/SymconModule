# SymconSnmp

Implementierung eines Snmp Clientes in IP-Symcon

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

  Beschreibung folgt 

## 2. Voraussetzungen

 - IPS ab Version 4.3  
 
## 3. Installation

   **ab IPS 4.3:**  
       `https://github.com/Acer90/SymconModule`  

## 4. Hinweise zur Verwendung

## 5. Einrichten 

    folgt

## 6. PHP-Befehlsreferenz

 (Keine PHP Funktionen)

## 7. Parameter / Modul-Infos

GUID des Modules (z.B. wenn Instanz per PHP angelegt werden soll):  

| Instanz          | GUID                                   |
| :--------------: | :------------------------------------: |
| Device  | {2F4FB7B0-AF13-46F1-9DEA-1DEBE0C3E324} |

Eigenschaften des 'Device' für Get/SetProperty-Befehle:  

| Eigenschaft                | Typ     | Standardwert | Funktion                                     |
| :------------------------: | :-----: | :----------: | :------------------------------------------: |
| SNMPIPAddress              | string  | 127.0.0.1    | Die IP-Adresse des SNMP-Servers              |
| SNMPTimeout                | int     | 1            | Timeout in Sekunden                          |
| SNMPInterval               | int     | 10           | Prüfinterval in Sekunden                     |
| SNMPVersion                | string  | 2c           | Eintragen der Snmp Serverversion             |
| SNMPCommunity              | string  | public       |                                              |
| SNMPSecurityName           | string  | SomeName     | Nur für Version 3!                           |
| SNMPAuthenticationProtocol | string  | SHA          | Nur für Version 3!                           |
| SNMPAuthenticationPassword | string  | SomeAuthPass | Nur für Version 3!                           |
| SNMPPrivacyProtocol        | string  | DES          | Nur für Version 3!                           |
| SNMPPrivacyPassword        | string  | SomePrivPass | Nur für Version 3!                           |
| SNMPEngineID               | int     | 0            | Nur für Version 3!                           |
| SNMPContextName            | string  |              | Nur für Version 3!                           |
| SNMPContextEngine          | int     | 0            | Nur für Version 3!                           |
| SNMPSpeedModify            | int     | 1            | Zur korreckten Berechung der Geschwindigkeit |
| Devices                    | string  |              | List alle eingetragenen OID´s                |

#### Devices

| Label                      | name    | Funktion                                                              |
| :------------------------: | :-----: | :-------------------------------------------------------------------: |
| OID                        | oid     | Hier können OID´s, oder Platzhalter eingetragen werden                |                                     
| Convert                    | typ     | Hier können Converter eingetragen werden                              |
| Speed                      | speed   | Zum berechen der Auslastung wird die maximalgeschwindigkeit angegeben |

#### Platzhalter

| Platzhalter                 | Beispiel             | Ausgabe                                                                           |
| :-------------------------: | :------------------: | :-------------------------------------------------------------------------------: |
| PortStatus100\|Portnummer    | PortStatus100\|01     | Gibt den Status des Portes in Offline, Wating, 1Mbit, oder 100Mbit aus.           |
| PortStatus1000\|Portnummer   | PortStatus1000\|01    | Gibt den Status des Portes in Offline, Wating, 1Mbit, 100Mbit, oder 1Gbit aus.    |

## 8. Datenaustausch

 (Kein Datenaustausch möglich)

## 9. Anhang

## 10. Lizenz

  IPS-Modul:  
  [GNU GENERAL PUBLIC LICENSE](http://www.gnu.org/licenses/)  
