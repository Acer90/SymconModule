# Chart
## Position
  Der Titel kann "Oben", "Links", "Rechts" oder "Unten" vom Chart platziert werden.

  <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Titel_Oben.png" width="300" />Chart_Titel_Oben

  <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Titel_links.png" width="100" height="200" />Chart_Titel_Links


## Achsen
### Anzeigen
   Wenn man in den Charts mit DataLabels arbeitet, ist es nicht immer notwendig, die Achsen anzeigen zu lassen. Daher kann man diese abschalten.

   ![Chart_Achsen_Anzeigen](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Achsen_anzeigen1.png) Aktiviert 
   ![Chart_Achsen_Anzeigen](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Achsen_Aus.png) Deaktiviert 

### Beschriftung anzeigen
Je nachdem, wo man sich die Einheit anzeigen will, ist es möglich, die Achsbeschriftung dementsprechend anzupassen. Wenn also der Chart mit DataLabels angezeigt wird, wo die Einheit dahinter steht, dann braucht man die Einheit nicht an der Achse. Wenn nur Werte oder gar keine DataLabels angezeigt werden, ist es sinnvoll, die Wert-Einheit an der Achse zu zeigen.
An der Achse können mehrere Varianten angezeigt werden. Entweder nur die Wert-Einheit, oder nur der Name des hinterlegten Profils oder beides, wobei dann die Wert-Einheit in Klammern gesetzt wird.

![Chart_Beschriftung anzeigen](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Beschriftung_anzeigen.png)

![Chart_Suffix](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Achsen_anzeigen.png) Suffix 
![Profil](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Achse_Profil.png) Profil
![Chart_Profil_Suffix](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Achse_Profil_Einheit.png) Profil(Suffix)

### Rand zeichnen
Diese Funktion "kästelt" den Chart ein. Dabei wird ein Rand um drei Seiten gezogen: Oben, Unten und Links. Die Rechte Seite bleibt offen.

![Chart_Rand ein](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Achsen_Rand.png) Eingeschaltet
![Chart_Rand aus](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Rand_aus.png) Abgeschaltet

### Im Diagrammbereich zeichnen
Um die auf der X-Achse vorhandenen Datenpunkte (im Beispiel die Zeitachse) bei großen Charts besser zu erkennen, gibt es die Möglichkeit an den Verschiedenen Datenpunkten Hilfslinien zu zeichnen. Diese Funktion hat auch Einfluss auf den Punkt "Rand zeichnen". Wird "Im Diagramm zeichnen" deaktiviert, zeichnet die Funktion "Rand zeichnen" nur den linken und unteren Rahmen.

![Chart_Zeichnen_Aus](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Diagrammbereich_Zeichnen_aus.png) Ausgeschaltet
![Chart_Zeichnen_ein](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Diagrammbereich_Zeichnen_an.png) Eingeschaltet

## Punkte
  Punkte werden immer dann eingesetzt, wenn ich im Chart z.B. sehen will, wann ein bestimmter Wert erhoben wurde. Der Eingang eines neuen Wertes wird 
  dann mittels eines individuell konfigurierbaren Punktes im Chart markiert.

![Chart_Punkte](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Punkte.png)

### Radius
Der Radius bestimmt die Größe des genutzten Punktes. Dabei ist es egal, ob es sich um einen Punkt, ein Kreuz, oder ähnliches handelt.

10 Punkte![Chart_Radius_10](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Radius_10_1.png) 
20 Punkte![Chart_Radius_20](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Radius_20.png) 

### Hover-Radius
Wenn man mit Datenpunkten arbeitet, so können bei Annäherung mit der Maus an den Datenpunkt Tooltips angezeigt werden, aus denen z.B. der aktuelle Wert ersichtlich wird. Mittels des "Hover-Radius" kann man einstellen, wie nah man an den Punkt kommen muss, um den Tooltip zu öffnen.

![Chart_Hover-Radius](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Hover-Radius.png)

### Punkte
Unter Punkte kann die Form der gezeigten Punkte variiert werden. Zur Auswahl stehen hier: Kreis, Kreuz(+), Kreuz(X), Strich, Line, Quadrat, Quadrat abgerundet, Raute, Stern und Dreieck.
Bei "Strich" und "Line" ist zu beachten, dass "Line" einen waagerechten Strich zentral an den Datenpunkt zeichnet. Bei "Strich" beginnt die gezeichnete Linie direkt am Datenpunkt und zieht sich dann nach rechts weiter.

Linie ![Chart_Punkt_Linie](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Punkt_line.png)
Strich ![Chart_Punkt_Strich](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Punkt_Strich.png)

## Legende
### Position
Die Legende kann natürlich ein- und ausgeblendet werden. Auch die Position kann bestimmt werden. So ist es möglich, die Legende oben, unten oder seitlich (rechts/links) anzuzeigen.

### Ausrichtung
Unter "Ausrichtung" lässt sich dann noch einstellen, ob die Legende z.B. Oben/Mittig oder z.B. Unten/Anfang (Links) dargestellt wird. Diese Positionierung geht auch bei der seitlichen Darstellung, wobei es sich folgendermaßen verhält: Anfang ist oben, Mittig ist Mittig und Ende ist unten. Somit kann man die Legende komplett seinen Vorstellungen positionieren.

Links/Ende ![Chart Links Ende](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Legende_links_ende.png)
Unten/Anfang ![Chart_Unten_Anfang](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Legende_unten_anfang.png)

### Boxbreite
Die Boxbreite bestimmt die Größe der Farbbox in der Legende:

Boxbreite 10: ![Chart_Box_10](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Box_10.png)
Boxbreite 120: ![Chart_Box_120](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Box_120.png)

## Tooltips
Mit den Tooltips kann man bei Berührung oder Annäherung an einen Datenpunkt Details zu diesem einblenden.

### Position
Mit der Position kann man bestimmen, ob ein Tooltip dicht am Datenpunkt angezeigt wird (am nächsten), oder etwas weiter entfernt von diesem (Average):

am nächsten ![Chart_Tooltip_Near](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Tooltip_Near.png)

Entfernt ![Chart_Tooltip_Nahe](https://github.com/Acer90/SymconModule/blob/alpha/imgs/Chart_Tooltip_Average.png)

### Modus
Über den Modus legt man fest, welche Daten angezeigt werden sollen:

<img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Index.png" width="200" />

Index: Die zusammenstehenden Punkte werden gelistet

<img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Datensatz.png" width="200" />

Datensatz: Die Historie zu einem Punkt wird gelistet

<img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Punkt.png" width="200" />

Punkt: Der Wert des betreffenden Punktes wird einzeln angezeigt

### Schrift

Alle bekannten Modifikationen (Farbe, Font, Größe)

### Eckradius
Über den Eckradius kann man der Tooltip-Bo weiche Kanten geben. 

Beispiele:

0  Punkte <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Eck_0.png" width="200" />

10 Punkte <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Eck_10.png" width="200" />

30 Punkte <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Eck_30.png" width="200" />

## Animation

### Dauer
Die Dauer bestimmt, wie schnell oder langsam die Charts aufgebaut werden. Als Beispiel nutze ich hier mal "Linear" als Animation:

500 Milisekunden <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Animation_500.gif" width="200" />

5000 Milisekunden <img src="https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Animation_5000.gif" width="200" />
### Übergangstyp
Der Übergangstyp legt die Art der Animation fest. Informationen zu den verschiedenen, möglichen Animationen findet Ihr unter: [Easing Functions Cheat Sheet](https://easings.net/)
