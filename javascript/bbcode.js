/* by masel
based on from http://www.der-webdesigner.net/tutorials/javascript/grundlagen/261-textfeld-mit-bbcodes-ausstatten.html
*/

var idCPcontainer = 'cpcontainer';       // ID der colorpicker Container
var idSPcontainer = 'spcontainer';        // ID der colorpicker Container
var idCPtable = 'colorpickertable';      // ID der colorpicker Tabelle
var idSPtable = 'smiliepickertable';     // ID der smiliepicker Tabelle

var rangeIE = null;

function insertProperty(idTextfeld,prop,val)
{
    insertText(idTextfeld,'[' + prop + '=' + val + ']', '[\/' + prop + ']');
}

function colorToHex(color) {
    if (color.substr(0, 1) === '#') {
        return color;
    }

    var m = /rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})/.exec(color);
    return m ? '#' + (1 << 24 | m[1] << 16 | m[2] << 8 | m[3]).toString(16).substr(1) : color;

}

// IE/Gecko-Weiche zum Einfügen von Text ins Textfeld
function insertText(idTextfeld, vor, nach)
{
    var textfeld = document.getElementById(idTextfeld);
    textfeld.focus();                                         // falls Cursor außerhalb war

    if(typeof document.selection != 'undefined') {              // für IE, auch Opera
        insertIE(textfeld, vor, nach);
    } else if (typeof textfeld.selectionStart != 'undefined') {   // Geckos (FF)
        insertGecko(textfeld, vor, nach);
    }
}

// Im "textfeld" Portionen "vor" und "nach" einfügen (IE)
function insertIE(textfeld, vor, nach)
{
    if(!rangeIE) rangeIE = document.selection.createRange();

    // nichts weiter tun, falls wir nicht im Textfeld sind
    if(rangeIE.parentElement() != textfeld) { rangeIE = null; return; }

    var alterText = rangeIE.text;

    // Auswahl um BBC ergänzen
    rangeIE.text = vor + alterText + nach;

    // Cursor neu setzen (wie SelfHTML)
    if (alterText.length == 0)
        rangeIE.move('character', -nach.length);
    else
        rangeIE.moveStart('character', rangeIE.text.length);

    rangeIE.select();
    rangeIE = null;
}


// Im "textfeld" Portionen "vor" und "nach" einfügen (Geckos)
function insertGecko(textfeld, vor, nach)
{
    von = textfeld.selectionStart;
    bis = textfeld.selectionEnd;

    // Text zerlegen
    anfang = textfeld.value.slice(0,   von);
    mitte  = textfeld.value.slice(von, bis);
    ende   = textfeld.value.slice(bis);        // der Rest des Strings

    // BBC einfügen und ins Textfeld schreiben
    textfeld.value = anfang + vor + mitte + nach + ende;

    // Cursor neu setzen
    if(bis - von == 0)
    {
        textfeld.selectionStart = von + vor.length;
        textfeld.selectionEnd   = textfeld.selectionStart;
    }
    else
    {
        textfeld.selectionEnd   = bis + vor.length + nach.length;
        textfeld.selectionStart = textfeld.selectionEnd;
    }
}

// Im IE die Textauswahl merken (onMouseDown im Farbwähler)
function getSelectionIE(idTextfeld)
{
    if (document.selection) {
        document.getElementById(idTextfeld).focus();
        rangeIE = document.selection.createRange();
    }
}

// Farbtabelle erzeugen und in Container-Div schreiben bzw. Tabelle löschen
function generateColorPicker(idTextfeld)
{
    if(document.getElementById(idSPcontainer)) {            //remove smiliepicker
        document.getElementsByTagName('body')[0].removeChild(document.getElementById(idSPcontainer));
    }
    if(document.getElementById(idCPcontainer)) {            //toggle colorpicker
        document.getElementsByTagName('body')[0].removeChild(document.getElementById(idCPcontainer));
        return;
    }

    var cpcontainer = document.createElement("div");
    cpcontainer.id = idCPcontainer;

    var strTabelle = '<table id="'+idCPtable+'" cellspacing="0">'+"\n";

    for(var r=0; r<257; r+=64) {
        strTabelle += "<tr>\n";

        for(var g=0; g<257; g+=64) {
            for(var b=0; b<257; b+=64) {
                strTabelle += '<td style="background-color:rgb('+r+','+g+','+b+')" '
                    + 'onclick="pickBgColor(this, \''+idTextfeld+'\')" '
                    + 'onmousedown="getSelectionIE(\''+idTextfeld+'\')"><\/td>'+"\n";
            }
        }
        strTabelle += "</tr>\n";
    }

    strTabelle += "<\/table>\n";

    document.body.appendChild(cpcontainer);
    document.getElementById(idCPcontainer).innerHTML += strTabelle;

    var TextfeldPosition = jQuery('#'+idTextfeld).offset();
    cpcontainer.style.position = 'absolute';
    cpcontainer.style.left = (TextfeldPosition.left+jQuery('#'+idTextfeld).outerWidth()/2-jQuery('#'+idCPcontainer).outerWidth()/2)+'px';
    cpcontainer.style.top = (TextfeldPosition.top-jQuery('#'+idCPcontainer).outerHeight()-10)+'px';
}

// Hintergrundfarbe des <td>-Elements auslesen und als [color=#rrggbb]...[color] einfügen
function pickBgColor(elem, idTextfeld)
{
    insertProperty(idTextfeld, 'color', colorToHex(elem.style.backgroundColor));
    generateColorPicker(idTextfeld);  //Colorpicker wieder aus
}


function generateSmiliePicker(idTextfeld)
{
    if(document.getElementById(idCPcontainer)) {            //remove colorpicker
        document.getElementsByTagName('body')[0].removeChild(document.getElementById(idCPcontainer));
    }
    if(document.getElementById(idSPcontainer)) {            //toggle smiliepicker
        document.getElementsByTagName('body')[0].removeChild(document.getElementById(idSPcontainer));
        return;
    }

    var spcontainer = document.createElement("div");
    spcontainer.id = idSPcontainer;

    var strTabelle = '<table id="'+idSPtable+'" cellspacing="1">'+"\n";
    strTabelle += "<tr>\n";

    var smilie_num = 1;
    var bbcode;
    for (bbcode in smilies) {

        strTabelle += '<td onclick="insertSmilie(\''+idTextfeld+'\', \''+bbcode+'\')">'+smilies[bbcode]+"</td>\n";

        if (smilie_num % 10 == 0) {
            strTabelle += "</tr>\n";
        }

        smilie_num += 1;
    }

    strTabelle += "<\/table>\n";

    document.body.appendChild(spcontainer);
    document.getElementById(idSPcontainer).innerHTML += strTabelle;

    var TextfeldPosition = jQuery('#'+idTextfeld).offset();
    spcontainer.style.position = 'absolute';
    spcontainer.style.left = (TextfeldPosition.left+jQuery('#'+idTextfeld).outerWidth()/2-jQuery('#'+idSPcontainer).outerWidth()/2)+'px';
    spcontainer.style.top = (TextfeldPosition.top-jQuery('#'+idSPcontainer).outerHeight()-10)+'px';
}

function insertSmilie(idTextfeld, bbcode)
{
    insertText(idTextfeld, bbcode, '');
    generateSmiliePicker(idTextfeld);  //Smiliepicker wieder aus
}
