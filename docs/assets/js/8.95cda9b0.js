(window.webpackJsonp=window.webpackJsonp||[]).push([[8],{332:function(t,e,i){"use strict";function a(t,e,i,a,n,s,r,c){var l,d="function"==typeof t?t.options:t;if(e&&(d.render=e,d.staticRenderFns=i,d._compiled=!0),a&&(d.functional=!0),s&&(d._scopeId="data-v-"+s),r?(l=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),n&&n.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(r)},d._ssrRegister=l):n&&(l=c?function(){n.call(this,(d.functional?this.parent:this).$root.$options.shadowRoot)}:n),l)if(d.functional){d._injectStyles=l;var h=d.render;d.render=function(t,e){return l.call(e),h(t,e)}}else{var o=d.beforeCreate;d.beforeCreate=o?[].concat(o,l):[l]}return{exports:t,options:d}}i.d(e,"a",(function(){return a}))},394:function(t,e,i){"use strict";i.r(e);var a=i(332),n=Object(a.a)({},(function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("ContentSlotsDistributor",{attrs:{"slot-key":t.$parent.slotKey}},[i("h1",{attrs:{id:"chart"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#chart"}},[t._v("#")]),t._v(" Chart")]),t._v(" "),i("h2",{attrs:{id:"titel-position"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#titel-position"}},[t._v("#")]),t._v(" Titel Position")]),t._v(" "),i("p",[t._v('Der Titel kann "Oben", "Links", "Rechts" oder "Unten" vom Chart platziert werden.')]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Chart Titel Oben")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_titel_oben.gif",height:"200"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Chart Titel links")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_titel_links.gif",height:"200"}})])])])]),t._v(" "),i("h2",{attrs:{id:"achsen"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#achsen"}},[t._v("#")]),t._v(" Achsen")]),t._v(" "),i("h3",{attrs:{id:"anzeigen"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#anzeigen"}},[t._v("#")]),t._v(" Anzeigen")]),t._v(" "),i("p",[t._v("Wenn man in den Charts mit DataLabels arbeitet, ist es nicht immer notwendig, die Achsen anzeigen zu lassen. Daher kann man diese abschalten.")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Achsen eingeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_Achsen_eingeblendet.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Achsen ausgeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_Achsen_ausgeblendet.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"beschriftung-anzeigen"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#beschriftung-anzeigen"}},[t._v("#")]),t._v(" Beschriftung anzeigen")]),t._v(" "),i("p",[t._v("Je nachdem, wo man sich die Einheit anzeigen will, ist es möglich, die Achsbeschriftung dementsprechend anzupassen. Wenn also der Chart mit DataLabels angezeigt wird, wo die Einheit dahinter steht, dann braucht man die Einheit nicht an der Achse. Wenn nur Werte oder gar keine DataLabels angezeigt werden, ist es sinnvoll, die Wert-Einheit an der Achse zu zeigen.\nAn der Achse können mehrere Varianten angezeigt werden. Entweder nur die Wert-Einheit, oder nur der Name des hinterlegten Profils oder beides, wobei dann die Wert-Einheit in Klammern gesetzt wird.")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("em",[t._v("Einheit")]),t._v(" eingeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_achsen_suffix.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("em",[t._v("Profil")]),t._v(" eingeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_achse_profil.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("em",[t._v("Profil & Einheit")]),t._v(" eingeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_achse_profil_einheit.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"rand-zeichnen"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#rand-zeichnen"}},[t._v("#")]),t._v(" Rand zeichnen")]),t._v(" "),i("p",[t._v('Diese Funktion "kästelt" den Chart ein. Dabei wird ein Rand um zwei Seiten gezogen: Unten und Links. Die Rechte Seite und oben bleiben offen.')]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Rand eingeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_rand_eingeblendet.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Rand ausgeblendet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_rand_ausgeblendet.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"im-diagrammbereich-zeichnen"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#im-diagrammbereich-zeichnen"}},[t._v("#")]),t._v(" Im Diagrammbereich zeichnen")]),t._v(" "),i("p",[t._v('Um die auf der X-Achse vorhandenen Datenpunkte (im Beispiel die Zeitachse) bei großen Charts besser zu erkennen, gibt es die Möglichkeit an den Verschiedenen Datenpunkten Hilfslinien zu zeichnen. Diese Funktion hat auch Einfluss auf den Punkt "Rand zeichnen". Wird "Im Diagramm zeichnen" deaktiviert, zeichnet die Funktion "Rand zeichnen" nur den linken und unteren Rahmen.')]),t._v(" "),i("p",[t._v("Wird dagegen Rand zeichnen ausgeschaltet, zeichnet das Modul nur die Zwischenschritte.")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Im Diagram zeichnen aktiviert")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_diagramm_zeichnen_aktiviert.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Im Diagram zeichnen deaktiviert")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_diagramm_zeichnen_deaktiviert.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Im Diagram zeichnen aktiviert, Rand zeichnen deaktiviert")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_diagramm_zeichnen_ohne_rand.gif"}})])])])]),t._v(" "),i("h2",{attrs:{id:"punkte"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#punkte"}},[t._v("#")]),t._v(" Punkte")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Punkte werden immer dann eingesetzt, wenn ich im Chart z.B. sehen will, wann ein bestimmter Wert erhoben wurde. Der Eingang eines neuen Wertes wird dann mittels eines individuell konfigurierbaren Punktes im Chart markiert.")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"radius"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#radius"}},[t._v("#")]),t._v(" Radius")]),t._v(" "),i("p",[t._v("Der Radius bestimmt die Größe des genutzten Punktes. Dabei ist es egal, ob es sich um einen Punkt, ein Kreuz, oder ähnliches handelt.")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Punktgröße")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("5 Punkte")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_5.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("10 Punkte")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_10.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("20 Punkte")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_20.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("40 Punkte")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_40.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"hover-radius"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#hover-radius"}},[t._v("#")]),t._v(" Hover-Radius")]),t._v(" "),i("p",[t._v('Wenn man mit Datenpunkten arbeitet, so können bei Annäherung mit der Maus an den Datenpunkt Tooltips angezeigt werden, aus denen z.B. der aktuelle Wert ersichtlich wird. Mittels des "Hover-Radius" kann man einstellen, wie nah man an den Punkt kommen muss, um den Tooltip zu öffnen.'),i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_hover.gif"}})]),t._v(" "),i("h3",{attrs:{id:"punkte-2"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#punkte-2"}},[t._v("#")]),t._v(" Punkte")]),t._v(" "),i("p",[t._v('Unter Punkte kann die Form der gezeigten Punkte variiert werden. Zur Auswahl stehen hier: Kreis, Kreuz(+), Kreuz(X), Strich, Line, Quadrat, Quadrat abgerundet, Raute, Stern und Dreieck.\nBei "Strich" und "Line" ist zu beachten, dass "Line" einen waagerechten Strich zentral an den Datenpunkt zeichnet. Bei "Strich" beginnt die gezeichnete Linie direkt am Datenpunkt und zieht sich dann nach rechts weiter.')]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Kreis")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_kreis.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Kreuz (+)")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_kreuz.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Kreuz (x)")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_x.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Strich")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_strich.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Linie")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_linie.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Quadrat")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_quadrat.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Quadrat abgerundet")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_abgerundet.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Raute")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_raute.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Stern")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_stern.gif"}})])]),t._v(" "),i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Dreieck")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_punkte_dreieck.gif"}})])])])]),t._v(" "),i("h2",{attrs:{id:"legende"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#legende"}},[t._v("#")]),t._v(" Legende")]),t._v(" "),i("h3",{attrs:{id:"position"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#position"}},[t._v("#")]),t._v(" Position")]),t._v(" "),i("p",[t._v("Die Legende kann natürlich ein- und ausgeblendet werden. Auch die Position kann bestimmt werden. So ist es möglich, die Legende oben, unten oder seitlich (rechts/links) anzuzeigen.")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Eingeblendet")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Ausgeblendet")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_eingeblendet.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_ausgeblendet.gif"}})])])])]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Oben")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Links")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Unten")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Rechts")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_oben.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_links.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_unten.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_rechts.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"ausrichtung"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#ausrichtung"}},[t._v("#")]),t._v(" Ausrichtung")]),t._v(" "),i("p",[t._v('Unter "Ausrichtung" lässt sich dann noch einstellen, ob die Legende z.B. Oben/Mittig oder z.B. Unten/Anfang (Links) dargestellt wird. Diese Positionierung geht auch bei der seitlichen Darstellung, wobei es sich folgendermaßen verhält: Anfang ist oben, Mittig ist Mittig und Ende ist unten. Somit kann man die Legende komplett seinen Vorstellungen entsprechend positionieren.')]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Anfang")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Mitte")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Ende")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_anfang.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_mittig.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_ende.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"boxbreite"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#boxbreite"}},[t._v("#")]),t._v(" Boxbreite")]),t._v(" "),i("p",[t._v("Die Boxbreite bestimmt die Größe der Farbbox in der Legende:")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("10")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("50")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("100")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_Box_10.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_Box_50.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_legende_Box_100.gif"}})])])])]),t._v(" "),i("h2",{attrs:{id:"tooltips"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#tooltips"}},[t._v("#")]),t._v(" Tooltips")]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Beschreibung")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Beispiel")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[t._v("Mit den Tooltips kann man bei Berührung oder Annäherung an einen Datenpunkt Details zu diesem einblenden.")]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"position-2"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#position-2"}},[t._v("#")]),t._v(" Position")]),t._v(" "),i("p",[t._v("Mit der Position kann man bestimmen, ob ein Tooltip dicht am Datenpunkt angezeigt wird (am nächsten), oder etwas weiter entfernt von diesem (Average):")]),t._v(" "),i("p",[i("em",[t._v("Beachte: dies funktioniert nur bei Charts mit mehreren Datenquellen")])]),t._v(" "),i("table",[i("thead",[i("tr",[i("th",{staticStyle:{"text-align":"left"}},[t._v("Am nächsten")]),t._v(" "),i("th",{staticStyle:{"text-align":"left"}},[t._v("Average (Entfernt)")])])]),t._v(" "),i("tbody",[i("tr",[i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_nah.gif"}})]),t._v(" "),i("td",{staticStyle:{"text-align":"left"}},[i("img",{attrs:{src:"https://raw.githubusercontent.com/Acer90/SymconModule/master/docs/images/chart_tooltipp_fern.gif"}})])])])]),t._v(" "),i("h3",{attrs:{id:"modus"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#modus"}},[t._v("#")]),t._v(" Modus")]),t._v(" "),i("p",[t._v("Über den Modus legt man fest, welche Daten angezeigt werden sollen:")]),t._v(" "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Index.png",width:"200"}}),t._v(" "),i("p",[t._v("Index: Die zusammenstehenden Punkte werden gelistet")]),t._v(" "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Datensatz.png",width:"200"}}),t._v(" "),i("p",[t._v("Datensatz: Die Historie zu einem Punkt wird gelistet")]),t._v(" "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Punkt.png",width:"200"}}),t._v(" "),i("p",[t._v("Punkt: Der Wert des betreffenden Punktes wird einzeln angezeigt")]),t._v(" "),i("h3",{attrs:{id:"schrift"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#schrift"}},[t._v("#")]),t._v(" Schrift")]),t._v(" "),i("p",[t._v("Alle bekannten Modifikationen (Farbe, Font, Größe)")]),t._v(" "),i("h3",{attrs:{id:"eckradius"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#eckradius"}},[t._v("#")]),t._v(" Eckradius")]),t._v(" "),i("p",[t._v("Über den Eckradius kann man der Tooltip-Bo weiche Kanten geben.")]),t._v(" "),i("p",[t._v("Beispiele:")]),t._v(" "),i("p",[t._v("0  Punkte "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Eck_0.png",width:"200"}})]),t._v(" "),i("p",[t._v("10 Punkte "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Eck_10.png",width:"200"}})]),t._v(" "),i("p",[t._v("30 Punkte "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_ToolTip_Eck_30.png",width:"200"}})]),t._v(" "),i("h2",{attrs:{id:"animation"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#animation"}},[t._v("#")]),t._v(" Animation")]),t._v(" "),i("h3",{attrs:{id:"dauer"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#dauer"}},[t._v("#")]),t._v(" Dauer")]),t._v(" "),i("p",[t._v('Die Dauer bestimmt, wie schnell oder langsam die Charts aufgebaut werden. Als Beispiel nutze ich hier mal "Linear" als Animation:')]),t._v(" "),i("p",[t._v("500 Milisekunden "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Animation_500.gif",width:"200"}})]),t._v(" "),i("p",[t._v("5000 Milisekunden "),i("img",{attrs:{src:"https://github.com/Acer90/SymconModule/blob/alpha/imgs/Gauge_Animation_5000.gif",width:"200"}})]),t._v(" "),i("h3",{attrs:{id:"ubergangstyp"}},[i("a",{staticClass:"header-anchor",attrs:{href:"#ubergangstyp"}},[t._v("#")]),t._v(" Übergangstyp")]),t._v(" "),i("p",[t._v("Der Übergangstyp legt die Art der Animation fest. Informationen zu den verschiedenen, möglichen Animationen findet Ihr unter: "),i("a",{attrs:{href:"https://easings.net/",target:"_blank",rel:"noopener noreferrer"}},[t._v("Easing Functions Cheat Sheet"),i("OutboundLink")],1)])])}),[],!1,null,null,null);e.default=n.exports}}]);