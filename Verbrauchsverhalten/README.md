# Verbrauchsverhalten
Das Modul errechnet den wahrscheinlichen Verbrauch mit einer Außentemperatur-Variable und einer Zähler-Variable.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Errechnet aus zwei Variablen einen wahrscheinlichen Verbrauch

### 2. Voraussetzungen

- IP-Symcon ab Version 6.0

### 3. Software-Installation

* Über den Module Store das 'Verbrauchsverhalten'-Modul installieren.
* Alternativ über das Module Control folgende URL hinzufügen `https://github.com/symcon/Verbrauchsverhalten`

### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'Verbrauchsverhalten'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                         | Beschreibung
---------------------------- | ------------------
Variable für Außentemperatur | Variable für die Außentemperatur
Variable für den Zähler      | Variable für den Zähler
Perioden Stufe               | Setzt in welchem Zeitraum die Daten sein sollen
Limit                        | Setzt wie viele Datensätze für die Regression genutzt werden sollen
Intervall                    | Setzt in welchen Zeitraum die Variablen neu berechnet werden sollen
Berechnen                    | Button um die Variablen manuell neu zu setzen

### 5. Statusvariablen

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name                           | Typ   | Beschreibung
------------------------------ | ----- | ------------
Erwartung der aktuelle Periode | float | Zeigt den erwarteten Verbrauch der laufenden Periode an
Erwartung der letzten Periode  | float | Zeigt den erwarteten Verbrauch der letzten Periode an
Wert der aktuellen Periode     | float | Zeigt den Wert der laufenden Periode an
Wert der letzten Periode       | float | Zeigt den Wert der letzten Periode an
Prozent der aktuellen Periode  | float | Zeigt in wie weit sich der Tatsächliche Verbrauch vom erwarteten Wert prozentual unterscheiden
Prozent der letzten Periode    | float | Zeigt in wie weit sich der Tatsächliche Verbrauch vom erwarteten Wert prozentual unterscheiden

### 6. WebFront

Die Funktionalität, die das Modul im WebFront bietet.

### 7. PHP-Befehlsreferenz

`boolean VBV_setData(integer $InstanzID);`
Die Funktion errechnet die erwarteten Werte.

Beispiel:
`LR_BeispielFunktion(12345);`