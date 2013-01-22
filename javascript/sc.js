/*
    für m_sc.php
 */

function SondenRechnen() {
    "use strict";
    var X11Benoetigt, TerminusBenoetigt, X13Benoetigt;

    document.getElementById('SD01').value = document.getElementById('SD01').value.slice(0, 4);
    document.getElementById('SD02').value = document.getElementById('SD02').value.slice(0, 4);

    var AnzahlSD01 = parseInt(document.getElementById('SD01').value || 0, 10) || 0;
    var AnzahlSD02 = parseInt(document.getElementById('SD02').value || 0, 10) || 0;

    X11Benoetigt = Math.ceil(AnzahlSD01 + (AnzahlSD02 * 2.5) + 20);
    TerminusBenoetigt = Math.ceil((AnzahlSD01 / 1.2) + (AnzahlSD02 * 2.5 / 1.2) + 10);
    X13Benoetigt = Math.ceil((AnzahlSD01 / 2) + (AnzahlSD02 * 2.5 / 2) + 8);

    document.getElementById('X11').firstChild.data = X11Benoetigt.toString();
    document.getElementById('Terminus').firstChild.data = TerminusBenoetigt.toString();
    document.getElementById('X13').firstChild.data = X13Benoetigt.toString();

}

