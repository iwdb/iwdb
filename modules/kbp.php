<?php

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

define('IWParser', 1);

global $daten;

if (isset($_POST['Eingabe'])) {
    if (($_POST['Eingabe']) == "") {
        echo "<div class='system_error'>Keinen KB-Link eingetragen!</div>";
    } else {

        // KBs raussuchen
        preg_match_all('#www\.icewars\.de/portal/kb/de/kb\.php\?id=[\d]+&md_hash=[\w]{32}#', $_POST['Eingabe'], $kblinks);

        foreach ($kblinks[0] as $kblink) {

            $temp_daten = '';

            if (!empty($kblink)) {
                if ($handle = fopen("http://" . $kblink . "&typ=bbcode", "r")) {
                    while (!@feof($handle)) {
                        $temp_daten .= @fread($handle, 512);
                    }
                    @fclose($handle);
                }

                $temp_daten = mb_convert_encoding($temp_daten, 'UTF-8',
                    mb_detect_encoding($temp_daten, 'UTF-8, ISO-8859-1', true)
                );

                $suchen     = '#(\[tr\]\[td\])((?:kleine|mittlere|große|DN)(?: planetare| orbitale)? Werft)(\[/td\]\[td colspan=3\])([\d]+)(\[/td\]\[/tr\])#';
                $ersetzen   = '$1[color=red]$2[/color]$3[color=red]$4[/color]$5';
                $temp_daten = preg_replace($suchen, $ersetzen, $temp_daten);

                $suchen     = array('[td colspan=4]');
                $ersetzen   = array('[td]');
                $temp_daten = str_replace($suchen, $ersetzen, $temp_daten);

                $suchen     = array('[td colspan=3]');
                $ersetzen   = array('[td]');
                $temp_daten = str_replace($suchen, $ersetzen, $temp_daten);

                // Von smf auf phpbb umwandeln
                // $suchen = array('[quote]', '[/quote]', '[table]', '[/table]', '[tr]', '[/tr]', '[td]', '[/td]', '[td colspan=4]', '[td colspan=3]');
                // $ersetzen = array('', '', '</span><table width="90%" cellspacing="1" cellpadding="3" border="0" align="center"><tr> 	  <td><span class="genmed"><b>Kampfbericht:</b></span></td>	</tr>	<tr>	  <td><table class="quote">', '</table></td>	</tr></table><span class="postbody">', '<tr>', '</tr>', '<td>', '</td>', '<td colspan="4">', '<td colspan="3">');
                // $temp_daten = str_replace($suchen, $ersetzen, $temp_daten);

                // Zeit aus xml rauspicken
                $xml = simplexml_load_file_ex('http://' . $kblink . '&typ=xml');


                $daten['KBs'][] = array(
                    'Zeit'    => (int)$xml->timestamp['value'],
                    'Bericht' => $temp_daten,
                    'Link'    => $kblink,
                );
            }
        }

        asort($daten['KBs']);
        echo 'Den unten stehenden Text per "copy\'n\'paste" in jedes beliebige Forum mit BB-Code-Support posten.<br><br>';
        foreach ($daten['KBs'] as $kb) {
            echo htmlspecialchars($kb['Bericht'], ENT_QUOTES, 'UTF-8');
            echo '<br>_______________________________________________________<br><br>';
        }
    }
}