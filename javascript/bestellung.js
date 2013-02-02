/*
    für m_bestellung und m_bestellung_schiffe.php
 */

function updateCoords() {
    "use strict";
    var strPlanet, strPlanetCoords, strPlanetCoordsGala, strPlanetCoordsSystem, strPlanetCoordsPlanet;

    //Limitieren des Input auf max 4 Stellen
    strPlanet = document.getElementById('planetcoords_select').value;
    if (strPlanet !== 0) {      //nicht '(anderer)' ausgewählt
        strPlanetCoords = strPlanet.split(' ')[0];
        strPlanetCoordsGala = strPlanet.split(':')[0];
        strPlanetCoordsSystem = strPlanet.split(':')[1];
        strPlanetCoordsPlanet = strPlanet.split(':')[2];
    }

    //Koordinatenfelder ändern
    document.getElementById('coords_gal_input').value = strPlanetCoordsGala;
    document.getElementById('coords_sys_input').value = strPlanetCoordsSystem;
    document.getElementById('coords_planet_input').value = strPlanetCoordsPlanet;

}

