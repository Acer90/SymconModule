# Chart
## Titel Position
  Der Titel kann "Oben", "Links", "Rechts" oder "Unten" vom Chart platziert werden.

  Beschreibung|Beispiel
  :-----|:---
  Chart Titel Oben|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_titel_oben.gif" height="200" />
  Chart Titel links|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_titel_links.gif" height="200" />
  
## Achsen
### Anzeigen
   Wenn man in den Charts mit DataLabels arbeitet, ist es nicht immer notwendig, die Achsen anzeigen zu lassen. Daher kann man diese abschalten.

   Beschreibung|Beispiel
   :----|:----
   Achsen eingeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_Achsen_eingeblendet.gif" />
   Achsen ausgeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_Achsen_ausgeblendet.gif" />

### Beschriftung anzeigen
Je nachdem, wo man sich die Einheit anzeigen will, ist es möglich, die Achsbeschriftung dementsprechend anzupassen. Wenn also der Chart mit DataLabels angezeigt wird, wo die Einheit dahinter steht, dann braucht man die Einheit nicht an der Achse. Wenn nur Werte oder gar keine DataLabels angezeigt werden, ist es sinnvoll, die Wert-Einheit an der Achse zu zeigen.
An der Achse können mehrere Varianten angezeigt werden. Entweder nur die Wert-Einheit, oder nur der Name des hinterlegten Profils oder beides, wobei dann die Wert-Einheit in Klammern gesetzt wird.

Beschreibung|Beispiel
:----|:------
*Einheit* eingeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_achsen_suffix.gif" />
*Profil* eingeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_achse_profil.gif" />
*Profil & Einheit* eingeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_achse_profil_einheit.gif" />

### Rand zeichnen
Diese Funktion "kästelt" den Chart ein. Dabei wird ein Rand um zwei Seiten gezogen: Unten und Links. Die Rechte Seite und oben bleiben offen.

Beschreibung|Beispiel
:----|:----
Rand eingeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_rand_eingeblendet.gif" />
Rand ausgeblendet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_rand_ausgeblendet.gif" />

### Im Diagrammbereich zeichnen

Um die auf der X-Achse vorhandenen Datenpunkte (im Beispiel die Zeitachse) bei großen Charts besser zu erkennen, gibt es die Möglichkeit an den Verschiedenen Datenpunkten Hilfslinien zu zeichnen. Diese Funktion hat auch Einfluss auf den Punkt "Rand zeichnen". Wird "Im Diagramm zeichnen" deaktiviert, zeichnet die Funktion "Rand zeichnen" nur den linken und unteren Rahmen.

Wird dagegen Rand zeichnen ausgeschaltet, zeichnet das Modul nur die Zwischenschritte.

Beschreibung|Beispiel
:----|:----
Im Diagram zeichnen aktiviert|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_diagramm_zeichnen_aktiviert.gif" />
Im Diagram zeichnen deaktiviert|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_diagramm_zeichnen_deaktiviert.gif" />
Im Diagram zeichnen aktiviert, Rand zeichnen deaktiviert|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_diagramm_zeichnen_ohne_rand.gif" />

## Punkte
  Beschreibung|Beispiel
  :---|:---
  Punkte werden immer dann eingesetzt, wenn ich im Chart z.B. sehen will, wann ein bestimmter Wert erhoben wurde. Der Eingang eines neuen Wertes wird dann mittels eines individuell konfigurierbaren Punktes im Chart markiert.|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte.gif" />

### Radius
Der Radius bestimmt die Größe des genutzten Punktes. Dabei ist es egal, ob es sich um einen Punkt, ein Kreuz, oder ähnliches handelt.

Punktgröße|Beispiel
:---|:---
5 Punkte|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_5.gif" />
10 Punkte|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_10.gif" />
20 Punkte|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_20.gif" />
40 Punkte|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_40.gif" />


### Hover-Radius
Wenn man mit Datenpunkten arbeitet, so können bei Annäherung mit der Maus an den Datenpunkt Tooltips angezeigt werden, aus denen z.B. der aktuelle Wert ersichtlich wird. Mittels des "Hover-Radius" kann man einstellen, wie nah man an den Punkt kommen muss, um den Tooltip zu öffnen.<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_hover.gif" />

### Punkte
Unter Punkte kann die Form der gezeigten Punkte variiert werden. Zur Auswahl stehen hier: Kreis, Kreuz(+), Kreuz(X), Strich, Line, Quadrat, Quadrat abgerundet, Raute, Stern und Dreieck.
Bei "Strich" und "Line" ist zu beachten, dass "Line" einen waagerechten Strich zentral an den Datenpunkt zeichnet. Bei "Strich" beginnt die gezeichnete Linie direkt am Datenpunkt und zieht sich dann nach rechts weiter.

Beschreibung|Beispiel
:----|:----
Kreis|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_kreis.gif" />
Kreuz (+)|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_kreuz.gif" />
Kreuz (x)|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_x.gif" />
Strich|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_strich.gif" />
Linie|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_linie.gif" />
Quadrat|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_quadrat.gif" />
Quadrat abgerundet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_abgerundet.gif" />
Raute|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_raute.gif" />
Stern|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_stern.gif" />
Dreieck|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_dreieck.gif" />

## Legende
### Position
Die Legende kann natürlich ein- und ausgeblendet werden. Auch die Position kann bestimmt werden. So ist es möglich, die Legende oben, unten oder seitlich (rechts/links) anzuzeigen.
Eingeblendet|Ausgeblendet
:----|:----
<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_eingeblendet.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_ausgeblendet.gif" />

Oben|Links|Unten|Rechts
:----|:----|:----|:----
<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_oben.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_links.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_unten.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_rechts.gif" />

### Ausrichtung
Unter "Ausrichtung" lässt sich dann noch einstellen, ob die Legende z.B. Oben/Mittig oder z.B. Unten/Anfang (Links) dargestellt wird. Diese Positionierung geht auch bei der seitlichen Darstellung, wobei es sich folgendermaßen verhält: Anfang ist oben, Mittig ist Mittig und Ende ist unten. Somit kann man die Legende komplett seinen Vorstellungen entsprechend positionieren.
Anfang|Mitte|Ende
:----|:----|:----
<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_anfang.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_mittig.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_ende.gif" />

### Boxbreite
Die Boxbreite bestimmt die Größe der Farbbox in der Legende:

10|50|100
:----|:----|:----
<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_Box_10.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_Box_50.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_Box_100.gif" />

## Tooltips
Beschreibung|Beispiel
:----|:----
Mit den Tooltips kann man bei Berührung oder Annäherung an einen Datenpunkt Details zu diesem einblenden.|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp.gif" />

### Position
Mit der Position kann man bestimmen, ob ein Tooltip dicht am Datenpunkt angezeigt wird (am nächsten), oder etwas weiter entfernt von diesem (Average):

*Beachte: dies funktioniert nur bei Charts mit mehreren Datenquellen und im index-modus*

Am nächsten|Average (Entfernt)
:----|:----
<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_nah.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_fern.gif" />

### Modus
Über den Modus legt man fest, welche Daten angezeigt werden sollen:

Beschreibung|Beispiel
:----|:----
Index: Die zusammenstehenden Punkte werden gelistet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_index.gif" />
Datensatz: Die Historie zu einem Punkt wird gelistet|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_datensatz.gif" />
Punkt: Der Wert des betreffenden Punktes wird einzeln angezeigt|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_punkt.gif" />

### Schrift

Alle bekannten Modifikationen (Farbe, Font, Größe, Schriftart). Die Schriftarten sind unter [Google Fonts](./Start.Google-Fonts)  einsehbar.

### Eckradius
Über den Eckradius kann man der Tooltip-Bo weiche Kanten geben. 

Beispiele:

0|10|30
:----|:----|:----
<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_ecke_0.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_ecke_10.gif" />|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_ecke_30.gif" />

## Animation

### Dauer
Die Dauer bestimmt, wie schnell oder langsam die Charts aufgebaut werden. Als Beispiel nutze ich hier mal "Linear" als Animation:
Dauer in ms|Beispiel
:----|:----
10ms|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_animation_speed_100.gif" />
500ms|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_animation_speed_500.gif" />
1000ms|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_animation_speed_1000.gif" />
5000ms|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_animation_speed_5000.gif" />

### Übergangstyp
Der Übergangstyp legt die Art der Animation fest. Informationen zu den verschiedenen, möglichen Animationen findet Ihr unter: [Easing Functions Cheat Sheet](https://easings.net/)

## Datenlabels

Erläuterung|Beispiel
:----|:----
Datenlabels dienen zur permanenten Veranschaulichung der erhobenen Werte|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_datenlabel.gif" />
Wenn die Daten häufig geändert oder abgerufen werden, kann es zu einem Überlagern der Labels kommen. Hier wäre es sinnvoller, auf Tooltipps zu setzen. oder den Rythmus der Anzeige anzupassen (dazu später mehr).|<img src="https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_datenlabel_viel.gif" />

