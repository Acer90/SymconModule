# Gauge
## Vorlage
 Beschreibung| Bild
:-------------|----------------:
Kreisförmigen Anzeige (CanvasGauges-Radial)|<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_radial.gif" width="200" />
Waagerechte, gerade Anzeige (CanvasGauges-Linear)|<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_Waagerecht.gif" width="200" />
Senkrechte, gerade Anzeige (CanvasGauges-Linear(vertical))|<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_Senkrecht.gif" width="50" />
Kompass (Anzeige von z.B. Windrichtung)|<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_Windrose.gif" width="200" />

## Titel
Erklärt sich wohl von selber. :wink:

## Platte
 Beschreibung| Bild
:-------------|----------------:
Gestaltet den Hintergrund. Die Platte wird immer als ganzes dargestellt. Sollte also nicht zum Einsatz kommen, wenn Ihr nur einen Halbkreis darstellen wollt:|<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_Platte.gif" width="300" />
Bei der Platte kann man den Farbverlauf gestalten. Dabei ist Haupt die Farbe des Zentrums und Ende die Farbe am Rand. Zusätzlich kann man noch den Transparent-Level bestimmen|<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_Platte_Verlauf.gif" width="500" />

## Nadel/Zeiger
Die Nadel definiert das Design des Anzeigers. Hier gibt es zwei Formen:
Der Pfeil geht immer über die Mitte der Gauge hinaus oder endet in der Mitte. Er stellt eine Kompassnadel dar:

![Gauge_Needle](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Needle.png)

Dabei bestimmt der Startpunkt die Länge der gegenpoligen Seite (Süd). Der Endpunkt Die Länge der Nordnadel:

<img src="https://raw.githubusercontent.com/Burki24/SymconModule/gh-pages/images/Gauge_Platte_Verlauf.gif" width="450" />

Ergibt demzufolge diese Nadel:

![Gauge_Needle_Test](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Needle_Test.png)

Der Zeiger(Linie) Ist eine Pegelanzeige:
Hier bestimmt der Startpunkt die Entfernung vom Mittelpunkt und das Ende die Nähe zum Rand. Mit der Breite bestimmen wir die Dicke des Zeigers:

![Gauge_Needle_Line](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Needle_Line.png)

## Wert
Hier kann definiert werden, mit welcher Farbe/Schrift der Wert als Text UNTER der Gauge dargestellt wird. WICHTIG: Wenn Ihr eine Halbkreis-Gauge entwerft, solltet Ihr die Nutzung der Wert-Anzeige vermeiden. Diese erscheint nicht direkt unter dem Halbkreis, sondern unterhalb des gedachten Vollkreis, also weit entfernt von der dargestellten Gauge:

![Gauge_Wert](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Wert.png)

Das lässt sich auch nicht anders ausrichten.

## Fortschrittsbalken
Neben der Nadel/Zeigerdarstellung bietet die Gauge noch die Anzeige eines Fortschrittbalkens:

![Gauge_Fortschritt](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Fortschritt.png)

Ihr seht am äußeren Rand der Skala einen Balken. Dem können zwei Farben zugeordnet werden:
Hauptfarbe: Die Farbe, die den gesamten Balken darstellt
Fortschritt: Farbe, die bis zur Position der Nadel/Zeiger angezeigt wird. Daneben kann man über die Breite noch die Stärke des Balkens definieren (für die, die es heftig wollen):

![Gauge_Progress_Fat](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Progressbar_Fat.png)

Wer jetzt keine Nadel/Zeiger mag, kann auch nur mit dem Fortschrittsbalken arbeiten:

![Gauge_Progress_without_Needle](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Progressbar_without_Needle.png)

Dazu wird die Nadel/Zeiger einfach ausgeschaltet.

## Schritte/Zwischenwerte
**WICHTIG: Die Zwischenschritte sind unabhängig von den vergebenen Maximalwerten der Skala. Wenn Ihr also bei min. und max. die Rahmenbedingungen für die Anzeige der Werte bestimmt hat dass keinen Einfluss auf die hier vergebenen „Strokes“.**

**Wenn Ihr mit Highlights arbeiten wollt, achtet darauf, dass ihr "Exact ticks" aktiviert habt, sonst kommt es zu Fehldarstellungen.**

Einer der interessantesten Punkte bei den Einstellungen. Hier wird die Skala definiert und z. B. die Warnbereiche:
Als erstes Bestimmt Ihr hier die Schritte:

![Gauge_Steps](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Steps.png)

Danach die Highlights, also die farblich hervorgehobenen Bereiche in der Skala:

![Gauge_HighLights](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Highlights.png)

Diese beiden Konfigurationen ergeben praktisch die Skala, die Ihr oben seht. Farbwahl und hervorgehobene Bereiche könnt Ihr komplett selber festlegen.

Dann Könnt Ihr noch die Menge der Zwischenschritte (ohne Wertanzeige) bestimmen:

30 Zwischenschritte:
![Gauge_mini_Steps_30](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_mini_Steps_30.png)

2 Zwischenschritte:
![Gauge_mini_Steps_2](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_mini_Steps_2.png)

**WICHTIG: Hier müsst Ihr mind. 2 angeben. Bei 1 wird kein Zwischenschritt angezeigt.**

### Neue Funktion - Exact Ticks

Wenn in der Skala mit dynamischen Zwischenschritten gearbeitet werden soll (z.B. für einen Detailbereich) muss dieser Punkt aktiviert werden. Ansonsten kommt es zu einer linearen Darstellung der Schritte, egal, welchen Wert sie auf der Skala darstellen sollen.

Als Beispiel:
<img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/GAUGE_Exact_Ticks.png" width="400" />

Durch die lineare Skala wird bei dem Wert 23 die Nadel irgendwo bei dem Wert 11 landen (linkes Gauge). Da wir aber in den Detailbereichen eine genauere Anzeige brauchen, aktivieren wir die Funktion "Exact Ticks". Nun werden die von uns gesetzten Zwischenschritte korrekt auf der Skala verzeichnet und die Nadel zeigt die zum Wert passende Position an (rechtes Gauge).
Bei der linearen Gauge sieht das Ganze dann so aus:

<img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Exact_Ticks_Linear.png" width="500" />

## Farben der Skala

![Gauge_Scala_Color](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Scale_Color.png)

## Einstellungen der *Radialanzeige*

Hier wird der Radial-Gauge bestimmt. Die Werte werden in Grad angegeben

Bei einem Halbkreis Fange ich bei Startwinkel 90° an und möchte eine Skala von 180° haben (Winkel der Anzeige):

![Gauge_half_Circle](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_half_Circle.png)

Wenn ich einen Start und einen Endpunkt haben möchte, kann ich das mit verschieben der Winkel erreichen. Wenn ich also den Startwinkel auf 45° und den Winkel der Anzeige auf 270° festlege, sieht die Gauge so aus:

![Gauge_Scale_individual_Circle](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Scale_Round.png)

## Einstellungen der *Linearanzeige*
Seite Schritte bestimmt den Grundlegenden Aufbau des Linear-Gauge

Beidseitig:

![Gauge_Scale_Linear](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Scale_Linear.png)

Links (Oben):

![Gauge_Linear_Up](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Scale_Linear_up.png)

Rechts (Unten):

![Gauge_Linear_Down](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Scale_Linear_down.png)

Bei den Werten (Nummern) verhält es sich genauso. Wählt Ihr die Senkrechte Variante gilt hier das Rechts/Links.

Auch bei den Linearen Gauges funktionieren Nadel und Fortschrittsbalken gleichermaßen.

## Animation

Die Gauges sind animiert (Ausschlag der Nadel/Zeiger und der Fortschrittsbalken). Hier könnt Ihr die Geschwindigkeit und den Effekt einstellen. Ich habe beispielsweise mal den Bounce-Effekt mit einer relativ langsamen Geschwindigkeit zur Veranschaulichung genommen:

![Gauge_Circle_Animation](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Circle_Animation.gif)![Gauge_Linear_Animation](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Linear_Animation.gif)

Dies sind nur Beispiele, am Besten, Ihr spielt hier selber mal mit den verschiedenen Effekten, die Zur Verfügung stehen.

Auf die anderen Punkte (Experte, Konfiguration und öffne Link) gehe ich hier nicht ein, da dies nur helfen soll, ansehnliche Gauges zu erstellen.