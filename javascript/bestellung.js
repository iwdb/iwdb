/*
 für m_bestellung und m_bestellung_schiffe.php
 */

function updateCoordsInput() {
    "use strict";
    var strPlanet, strPlanetCoords, strPlanetCoordsGala, strPlanetCoordsSystem, strPlanetCoordsPlanet;

    strPlanet = document.getElementById('planetcoords_select').value;
    if (strPlanet !== '0') {      //nicht '(anderer)' ausgewählt
        strPlanetCoords = strPlanet.split(' ')[0];
        strPlanetCoordsGala = strPlanetCoords.split(':')[0];
        strPlanetCoordsSystem = strPlanetCoords.split(':')[1];
        strPlanetCoordsPlanet = strPlanetCoords.split(':')[2];

        //Koordinatenfelder ändern
        document.getElementById('coords_gal_input').value = strPlanetCoordsGala;
        document.getElementById('coords_sys_input').value = strPlanetCoordsSystem;
        document.getElementById('coords_planet_input').value = strPlanetCoordsPlanet;
    }

}

function updateCoordsSelect() {
    "use strict";

    var Planet;
    var Planets = document.getElementById('planetcoords_select').options;

    var selected = false;
    for (Planet = 0; Planet < Planets.length; Planet++) {
        var PlanetCoordsInput = document.getElementById('coords_gal_input').value + ':' + document.getElementById('coords_sys_input').value + ':' + document.getElementById('coords_planet_input').value;
        if (PlanetCoordsInput === Planets[Planet].value) {
            document.getElementById('planetcoords_select').value = PlanetCoordsInput;
            selected = true;
            break;
        }
    }
    if (selected === false) {
        document.getElementById('planetcoords_select').value = '0';
    }
}

