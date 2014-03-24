/*
 für m_frachtkappa.php
 */

"use strict";

function frachtkappaRechnen() {
    var class1kappa_benoetigt, class1kappa_vorhanden, class1kappa_nochbenoetigt, class2kappa_benoetigt, class2kappa_vorhanden, class2kappa_nochbenoetigt;

    class1kappa_benoetigt = (document.getElementById('eisen').value * 1)
        + (document.getElementById('stahl').value * 2)
        + (document.getElementById('chemie').value * 3)
        + (document.getElementById('vv4a').value * 4);

    class1kappa_vorhanden = (document.getElementById('systransen_vorhanden').value * 5000)
        + (document.getElementById('gorgols_vorhanden').value * 20000)
        + (document.getElementById('kamele_vorhanden').value * 75000)
        + (document.getElementById('flughunde_vorhanden').value * 400000);

    class2kappa_benoetigt = (document.getElementById('eis').value * 2)
        + (document.getElementById('wasser').value * 2)
        + document.getElementById('energie').value;

    class2kappa_vorhanden = (document.getElementById('luche_vorhanden').value * 2000)
        + (document.getElementById('eisbaeren_vorhanden').value * 10000)
        + (document.getElementById('waschbaeren_vorhanden').value * 50000)
        + (document.getElementById('seepferdchen_vorhanden').value * 250000);

    class1kappa_nochbenoetigt = class1kappa_benoetigt - class1kappa_vorhanden;
    class2kappa_nochbenoetigt = class2kappa_benoetigt - class2kappa_vorhanden;

    document.getElementById('class1kappatext').firstChild.data = number_format(class1kappa_benoetigt, 0, ',', '.');
    document.getElementById('class2kappatext').firstChild.data = number_format(class2kappa_benoetigt, 0, ',', '.');

    document.getElementById('systranstext').firstChild.data = number_format(Math.ceil(class1kappa_nochbenoetigt / 5000), 0, ',', '.');
    document.getElementById('gorgoltext').firstChild.data = number_format(Math.ceil(class1kappa_nochbenoetigt / 20000), 0, ',', '.');
    document.getElementById('kameltext').firstChild.data = number_format(Math.ceil(class1kappa_nochbenoetigt / 75000), 0, ',', '.');
    document.getElementById('flughundtext').firstChild.data = number_format(Math.ceil(class1kappa_nochbenoetigt / 400000), 0, ',', '.');

    document.getElementById('lurchtext').firstChild.data = number_format(Math.ceil(class2kappa_nochbenoetigt / 2000), 0, ',', '.');
    document.getElementById('eisbaertext').firstChild.data = number_format(Math.ceil(class2kappa_nochbenoetigt / 10000), 0, ',', '.');
    document.getElementById('waschbaertext').firstChild.data = number_format(Math.ceil(class2kappa_nochbenoetigt / 50000), 0, ',', '.');
    document.getElementById('seepferdchentext').firstChild.data = number_format(Math.ceil(class2kappa_nochbenoetigt / 250000), 0, ',', '.');
}

setInterval(function () {frachtkappaRechnen(); }, 500);