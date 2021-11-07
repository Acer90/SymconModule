# DoughnutPie
Als Erstes müsst Ihr Euch entscheiden, ob Ihr eine Torte oder einen Doughnut haben wollt. Dies regelt Ihr unter dem Punkt „Typ“. Diese Auswahl hat zum Teil Auswirkungen auf die folgenden Einstellungen.

## Titel
Anders als bei der Gauge kann bei dem Pie/Doughnut die Position des Titels verändert werden.

![PIE_Titel](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Titel.png)

Alles Andere ist hier selbsterklärend.

## Legende
Unter Legende kann das Aussehen der Auflistung der Datenpunkte modifiziert werden. Die möglichen Einstellungen hier sind selbsterklärend.

**Kurz nur zum Thema Schriftauswahl:**
Wir haben einige der von Google-Fonts ( Browse Fonts - Google Fonts) zur Verfügung gestellten Schriften als Vorauswahl in die Module integriert. Damit sollten alle Bedürfnisse abgedeckt sein. Von kondensed bis auffällig. Bitte schaut Euch immer an, welche Auswirkung die Schriftauswahl hat.
Am Besten macht es sich, über „öffne Link“ das Ergebnis Eurer Einstellungen direkt im Browser zu betrachten. Ihr braucht den Link nur einmalig aufrufen und er passt sich immer, wenn Ihr auf „Einstellungen übernehmen“ klickt an.
Der Punkt „Box Breite“ bestimmt in der Legende die Breite der Farbbox:

Boxbreite 10: ![PIE_BOX_10](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Box_10.png) Boxbreite 120: ![PIE_Box_120](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Box_120.png)

Position: Position der LEgende (LInks, rechts, oben, unten

![PIE_Legende_Links](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Legende_links.png)![PIE_Legende_Oben](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Legende_oben.png)

Ausrichtung: Legt die vertikale Position im Doughnut/Pie fest:

Links/Anfang:
![PIE_Legende_Anfang](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Legende_Vertikal_Anfang.png)

Links/Mitte:
![PIE_Legende_Mitte](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Legende_Vertikal_Mitte.png)

Links/Ende:
![PIE_Legende_Ende](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Legende_Vertikal_Ende.png)

## Tooltips
Wenn Ihr den Doughnut oder den Pie ohne Werte darstellen wollt, und nur die Daten sehen wollt, wenn Ihr auf das entsprechende Feld geht, dann kommen die Tooltips zum Einsatz:

![PIE_Tooltipps](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Tooltipps.png)

Dabei gibt es zwei Positionen der Tooltips: „Nearest“, also zentral im Bereich, so wie im Bild oben. Und „Average“ dementsprechend weit entfernt vom Bereich:

![PIE_Tooltip_Average](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Tooltipps_Average.png)

Bei den Tooltips könnt Ihr den Einzelwert (Point/Index) oder die Werte der gesamten Datenreihe (dataset) anzeigen lassen:

![PIE_Tooltip_Dataset](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Tooltipps_Dataset.png)

Als nettes Gimick könnt Ihr dann noch die Ecken des Tooltips mittels „Eckradius“ anpassen. Auf die Möglichkeiten der Schriftvariationen werde ich hier im Einzelnen nicht mehr eingehen, da ich diese als selbsterklärend erachte.

## Drehung (Rotation)
Die Drehung ein sehr brauchbares Gimmick. Wie Ihr seht, arbeite ich bei den Demo-Bildern immer nur mit einem Viertel Doughnut (Den Rest hab ich einfach mal weg gefuttert :stuck_out_tongue_closed_eyes:)
Okay, eigentlich nur, weil man mit nem Viertel weniger Platz braucht und gewisse Einstellungen besser Visualisieren kann.

Aber zu den Werten:

„Anfang“ gibt den Start-Winkel des Doughnuts an. 0° entspricht hier demzufolge Oben. 180° Also Unten. Die Länge gibt den Umfang vor. Damit wird also der Doughnut beschnitten. 360° gibt einen ganzen Doughnut, 270° einen Dreiviertel, 180° einen Halben und 90° logischerweise dann einen Viertel.

![PIE_Rotatio1_1](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Drehung_1.png)![PIE_Rotation_2](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Drehung_2.png)![PIE_Rotation_3](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Drehung_3.png)![PIE_Rotation_4](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Drehung_4.png)

Wenn mir also ein Viertel reicht, kann ich dann noch mit dem Anfang bestimmen, ob er Oben anfängt (Siehe Bilder oben) oder, ob er versetzt anfängt. Gebe ich also bei Anfang statt 0° 45° ein, sieht das Ganze so aus:

![PIE_Rotation_45](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Drehung_45_Grad.png)

Also eine Menge Möglichkeiten zum Spielen.

## Animation
Wie schon bei der Gauge, sind auch Doughnut und Pie animiert. Was das genau bedeutet zeige ich Euch an einem kleinen Beispiel. Bitte auch hier austesten, was geht (ist gar nicht so schwer).

![PIE_Animation](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Animation.gif)

Spätestens jetzt dürfte Jeder wissen, ich LIEBE Bouncing. :grinning:
Acer90 hat unter dem Punkt Animation den Link der Quelle im Modul gesetzt [ Easing Functions Cheat Sheet](https://easings.net/). Da erfahrt ihr im Detail, was bei welcher Animation passiert. Das erspart mir hier ne Menge Tipparbeit. Danke Acer90 :joy:

## DataLabels
Die DataLabels beschreiben die Werte, die in den einzelnen Datenbereichen angezeigt werden. Verankerung und Ausrichtung geben hier die Position in dem Datenbereich der Grafik an. Verankerung regelt die Position am:

Äußeren Rand (Ende)
![PIE_DataLabels_Ouside](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Datalabels_Outside.png)

Mittig (mittig)
![PIE_DataLabels_Center](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Datalabels_Center.png)

Inneren Rand (Anfang):
![PIE_DataLabels_Inside](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Datalabels_Inside.png)

Mit der „Ausrichtung“ kann man dann die Position noch fein justieren. So bedeutet zum Beispiel:
Verankerung: mittig | Ausrichtung: Rechts, das das Daten Label rechts des Mittelpunkts des Datenfeldes angezeigt wird.
Auch hier die große Bitte: Testet es aus. Jede Position im kleinsten Detail hier zu nennen, sprengt mal eben den Rahmen.

## Daten
Den meisten Platz in diesem kleinen Tutorial werde ich nämlich für die Datenpflege benötigen.
Um das mit den Daten verständlich zu machen, fangen wir mal bei Excel an :joy::
Wir haben ein Gitter mit Spalten und Zeilen. Jede Zeile entspricht einer Reihe von Daten. Dabei geht es nicht um archivierte Daten eines Zeitraums sondern vielmehr um Daten einer Aktorensorte:

![PIE_EXCEL](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Table_Excel.png)

Daraus basteln wir uns jetzt einen wunderschönen Doughnut und Kuchen. Wenn wir uns den Punkt „Daten“ ansehen, dann gibt es da noch den Punkt „Datasets“. Dadrunter finden wir den unscheinbaren, aber mächtigen „Hinzufügen“ Button. Einmal geklickt, öffnet sich ein neues Fenster:

![PIE_Listenelement](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Listenelement.png)

Die Reihenfolge gibt später die Position dieses Datenrings im Doughnut/Pie an. Also ob er ganz außen oder ganz innen sitzen soll.
Der Titel ist in dem Fall unserer Tabelle zu entnehmen und heißt im Beispiel:„Luftfeuchtigkeit“. Wenn das erledigt ist, geht es weiter. Wieder auf den Punkt "Zufügen"Klicken. Ein neues Fenster öffnet sich und hier werden dann die einzelnen Werte-Variablen hinterlegt:

![PIE_ListenElement_bearbeiten](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Listenelement_bearbeiten.png)

Also kommen hier als Listenelemente die Variablen für Die Luftfeuchtigkeit entsprechend der einzelnen Räume rein.
WICHTIG: JEDE Variable muss einen Namen und ein Profil erhalten:

![PIE_Var_Profil](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Var_Profil.png)

Wenn Alles korrekt gelaufen ist, sollte jetzt der erste Ring des Doughnuts/Pie erscheinen:

![PIE_Daten_1](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Daten_1.png)

Das Ganze machen wir dann noch mit der Temperatur.

![PIE_Daten_2](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Daten_Zwei.png)

Und zu guter Letzt noch mit der Helligkeit:

![PIE_Daten_3](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_Daten_drei.png)

Und schon hat man die ersten Doughnuts gebacken. Als Pie sieht das dann ähnlich aus:

![PIE_PIE_Daten](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_PIE_Daten_1.png)![PIE_PIE_Daten_360](https://github.com/Acer90/SymconModule/blob/alpha/imgs/PIE_PIE_Daten_360_Grad.png)

Natürlich kann man auch hier noch nette Gimmicks nutzen:
Reihenfolge: Sowohl in den Datasets, also auch in den Listenelementen kann ich die Reihenfolge bestimmen.
DataLabels kann ich ein und ausschalten und bei Bedarf kann ich die Eigenschaften der DataLabels pro Listenelement entgegen der globalen Vorgabe nochmal anpassen.
Es ist übrigens jedem selbst Überlassen, ob er die Werte nach Aktoren oder nach Einsatzort listet. Dies dient hier nur als Beispiel zur Veranschaulichung.