/*
    für m_sc.php
 */

function SondenRechnen() {
    "use strict";
    var X11Benoetigt, TerminusBenoetigt, X13Benoetigt;

    //fix missing maxLength limitation on number inputs
    if (document.getElementById('SD01').value.length > document.getElementById('SD01').maxLength) {
        document.getElementById('SD01').value = document.getElementById('SD01').value.slice(0, document.getElementById('SD01').maxLength);
    }
    if (document.getElementById('SD02').value.length > document.getElementById('SD02').maxLength) {
        document.getElementById('SD02').value = document.getElementById('SD02').value.slice(0, document.getElementById('SD02').maxLength);
    }

    var AnzahlSD01 = +document.getElementById('SD01').value;
    var AnzahlSD02 = +document.getElementById('SD02').value;

    //Input validieren
    if (isNaN(AnzahlSD01) || (AnzahlSD01 < 0)) {
        AnzahlSD01 = 0;
        document.getElementById('SD01').value = 0;
    }
    if (isNaN(AnzahlSD02) || (AnzahlSD02 < 0)) {
        AnzahlSD02 = 0;
        document.getElementById('SD02').value = 0;
    }

    //Anzahl Sonden berechnen
    X11Benoetigt = Math.ceil(AnzahlSD01 + (AnzahlSD02 * 2.5) + 20);
    TerminusBenoetigt = Math.ceil((AnzahlSD01 / 1.2) + (AnzahlSD02 * 2.5 / 1.2) + 10);
    X13Benoetigt = Math.ceil((AnzahlSD01 / 2) + (AnzahlSD02 * 2.5 / 2) + 8);

    //Ausgabe
    document.getElementById('X11').firstChild.data = X11Benoetigt.toString();
    document.getElementById('Terminus').firstChild.data = TerminusBenoetigt.toString();
    document.getElementById('X13').firstChild.data = X13Benoetigt.toString();

}

