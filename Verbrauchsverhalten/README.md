# Verbrauchsverhalten
Das Modul errechnet den wahrscheinlichen Verbrauch für diese Periode auf Basis einer Außentemperatur-Variable und einer Zähler-Variable mithilfe der Linearen Regression. Je mehr Werte für die Außentemperatur und den Zähler verfügbar sind, desto genauer kann der erwartete Verbrauch ermittelt werden.

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
Variable für Außentemperatur | Geloggte Variable für die Außentemperatur
Variable für den Zähler      | Geloggte Variable für den Zähler
Periode                      | Zeitraum für den die Berechnung durchgeführt werden soll
Limit                        | Maximale Anzahl der Datensätze die für die Regression genutzt werden sollen. 0 = Keine Limitierung
Intervall                    | Zeitintervall des Timers in dem die Variable erneut berechnet werden soll
Berechnen                    | Button, um die Variablen neu zu berechnen

### 5. Statusvariablen

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

#### Statusvariablen

Name                                   | Typ   | Beschreibung
-------------------------------------- | ----- | ------------
Erwartung der aktuelle Periode         | float | Zeigt den erwarteten Verbrauch auf Basis der Außentemperatur der laufenden Periode an
Erwartung der letzten Periode          | float | Zeigt den erwarteten Verbrauch auf Basis der Außentemperatur der letzten Periode an
Hochrechnung der aktuelle Periode      | float | Zeigt den hochgerechneten Verbrauch der laufenden Periode an
Hochrechnung der letzten Periode       | float | Zeigt den hochgerechneten Verbrauch der letzten Periode an
Wert der aktuellen Periode             | float | Zeigt den aktuellen Verbrauch der laufenden Periode an
Wert der letzten Periode               | float | Zeigt den Verbrauch der letzten Periode an
Prozent der aktuellen Periode          | float | Zeigt in wie weit sich der hochgerechnete Verbrauch vom erwarteten Wert prozentual unterscheiden
Prozent der letzten Periode            | float | Zeigt in wie weit sich der hochgerechnete Verbrauch vom erwarteten Wert prozentual unterscheiden
Bestimmtheitsmaß der aktuellen Periode | float | Genauigkeit der Erwartungsberechnung der laufenden Periode an
Bestimmtheitsmaß der letzten Periode   | float | Genauigkeit der Erwartungsberechnung der letzten Periode an


Die Erwartung erfolgt anhand der einfachen linearen Regression. [Mathematisch Erklärt][https://de.wikipedia.org/wiki/Lineare_Einfachregression]
Die Hochrechnung besteht wiederrum aus dem Durchschnittswert der Periode multipliziert mit der Periodenlänge. 

### 6. WebFront

Die Funktionalität, die das Modul im WebFront bietet.

### 7. PHP-Befehlsreferenz

`boolean VBV_setData(integer $InstanzID);`
Die Funktion errechnet die erwarteten Werte.

Beispiel:
`LR_BeispielFunktion(12345);`
