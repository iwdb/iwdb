/*
 für m_incomings.php
 */

"use strict";

jQuery(document).ready(function () {

    var timeOfData = 0;
    var getIncomingDataIntervalID;

    function updateIncomings() {
        var recievedData = '';

        jQuery.ajax({
            url: 'ajax.php',
            type: 'POST',
            cache: false,
            async: false,
            data: {
                action: 'getIncomings',
                timestamp: timeOfData
            },
            success: function (data, status, xhr) {
                if (xhr.status === 200) {
                    recievedData = JSON.parse(data);
                    if (recievedData.result === 'success') {
                        timeOfData = recievedData.time;
                        jQuery('#incomings_tabellen_container').html(recievedData.tables);
                    }
                }
            }
        });
    }

    /**
     * @return {boolean}
     */
    function updateFleetsavings(handle) {
        var action = '';
        var result = false;
        var jQhandle = jQuery(handle);
        var recievedData = '';

        if (jQhandle.hasClass('savedCheckbox')) {
            action = 'setSaved';
        } else if (jQhandle.hasClass('recalledCheckbox')) {
            action = 'setRecalled';
        }

        jQuery.ajax({
            url: 'ajax.php',
            type: 'POST',
            cache: false,
            async: false,
            data: {
                coords: jQhandle.val(),
                action: action,
                state: handle.checked
            },
            success: function (data, status, xhr) {
                if (xhr.status === 200) {
                    recievedData = JSON.parse(data);
                    if (recievedData.result === 'success') {
                        timeOfData = recievedData.time;

                        jQuery('#incomings_tabellen_container').html(recievedData.tables);

                        clearInterval(getIncomingDataIntervalID);
                        getIncomingDataIntervalID = setInterval(function () {
                            updateIncomings();
                        }, 10000);             //Interval Neustart, Aufruf alle 10 Sekunden
                        result = true;
                    }
                }
            }
        });

        return result;
    }

    updateIncomings();
    getIncomingDataIntervalID = setInterval(function () {
        updateIncomings();
    }, 10000);                    //Aufruf alle 10 Sekunden

    jQuery(document).on("change", '.savedCheckbox, .recalledCheckbox', function () {          //saved oder recalled Änderung
        if (updateFleetsavings(this) === false) {
            jQuery(this).prop('checked', !this.checked);        //Auswahl rückgängig machen, da Fehler beim übernehmen
        }
    });

    jQuery(".tablesorter").tablesorter();
});