/*
 für menu_default.php
 */

"use strict";

jQuery(document).ready(function () {
    var getOnlineUsersIntervalID;

    function updateOnlineUsers() {
        var recievedData = '';

        jQuery.ajax({
            url: 'ajax.php',
            type: 'POST',
            cache: false,
            async: true,
            data: {
                action: 'getOnlineUsers'
            },
            success: function (data, status, xhr) {
                if (xhr.status === 200) {
                    recievedData = JSON.parse(data);
                    if (recievedData.result === 'success') {
                        jQuery('#doc_usersonline').html('Online: ' + recievedData.data.numOnlineMember + ' (' + recievedData.data.strOnlineMember + ')');
                    }
                }
            }
        });
    }

    updateOnlineUsers();
    setInterval(function () { updateOnlineUsers(); }, 180000);                    //Aufruf alle 3 Minuten

});