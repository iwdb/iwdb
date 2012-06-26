<?php

//definition der Sounddateien (leer = global ausgeschaltet)
//nur mp3 Dateien möglich da anderes nciht unerstuetzt werden kann
//achten sie darauf, dass die Dtaie nicht zu gro? ist.
$soundnormal = 'auftrag.mp3';
$soundwichtig = 'auftrag.mp3';

$amodul = GetVar('action');

global $sid, $db_prefix;

$modulyes = FALSE;

//auselsen ob das Modul Sound haben darf
$sqlM = "SELECT sound FROM ".$db_prefix."menu WHERE action = '".$amodul."';";
	$resultM = $db->db_query($sqlM)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);
	$rowM = $db->db_fetch_array($resultM);

if ( isset($rowM['sound']) AND !empty($rowM['sound']) ) $modulyes = TRUE;

if (empty($amodul) OR $amodul == 'memberlogin2' ) {
	//beim Login soll da abgespielt werden?
	$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_login' ";
	$resultP = $db->db_query($sqlP)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
	$rowP = $db->db_fetch_array($resultP);  
	if ( isset($rowP['value']) AND !empty($rowP['value']) ) $modulyes = TRUE;
}

if (isset($soundnormal) AND isset($soundwichtig)) {
	if (!empty($user_id) AND ($user_id != 'guest') AND ($modulyes)) {
		//was soll abgespielt werden?
		$sqlS = "SELECT sound FROM ".$db_prefix."user WHERE id = '".$user_id."' ";
		$resultS = $db->db_query($sqlS)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlS);
		$rowS = $db->db_fetch_array($resultS);

		$sound_einst = $rowS['sound'];
  
		if (!empty($sound_einst)) {
			if ($sound_einst == 3 OR $sound_einst == 4) {
?>
<script type="text/javascript">
<!--
function Farbeaendern(Farbe) {
	if (document.all && !document.getElementById) // IE 4
		document.all['test1'].style.backgroundColor=Farbe;
		document.all['test2'].style.backgroundColor=Farbe;

	if (document.getElementById) // IE 5.x und NN 6
		document.getElementById("test1").style.backgroundColor=Farbe;
		document.getElementById("test2").style.backgroundColor=Farbe;
}

var bgColor = "#CCCCCC";

function nerv_mich() {
	if(bgColor == "#0000FF") {
		bgColor = "#FF0000";
		Farbeaendern(bgColor);
	}
	else {
		if(bgColor == "#FF0000") {
			bgColor = "#FFFF00";
			Farbeaendern(bgColor);
		}
		else {
			bgColor = "#0000FF";
			Farbeaendern(bgColor);
		}
	}

	window.setTimeout(nerv_mich, 1000);
}
window.setTimeout(nerv_mich, 500);

//-->
</script>
<?php
			}
			// Anstehende Aufträge zählen
			$anzauftrag = "";
			if(( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS )) {
				$sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
					" WHERE date_b2 < " . ( $config_date );
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				$row = $db->db_fetch_array($result);
				$anzahl = $row['anzahl'];
				$db->db_free_result($result);
				$sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
					" WHERE date_b2 < " . ( $config_date ) . " AND typ = 'Forschung'";
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				$row = $db->db_fetch_array($result);
				$anzahl_n = $row['anzahl'];
				$db->db_free_result($result);

				// Wurde gerade ein Auftrag erledigt? Falls ja, muss dieser von der
				// gerade ermittelten Zahl abgezogen werden.
				if($action == "sitterliste") {
					$erledigt = getVar('erledigt');

					if( !empty($erledigt)) {
						$anzahl = $anzahl - 1;
						$anzahl_n = $anzahl_n - 1;
					}
				}

				if($anzahl > 0) {
					$anzauftrag = " (" . $anzahl . " offen)";
				}
			}

			if (isset($anzahl) AND $anzahl > 0) {
				//hat der user den Sound an?
				$soundon = $sound_einst;
				if ( empty($soundon) OR $soundon == '1' OR $soundon == '3' ) {
					$sound = '';
				}
				else {
					$sound = $soundnormal;
					if ($anzahl_n > 0) {
						$sound = $soundwichtig;
					}
				}
?>

<div id="fadein"> 
	<table id="blinker" class="bordercolor" bgColor="red" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"> 
		<tbody> 
			<tr> 
				<td class="fadein" ID="test1"> 
<?php
				if (!empty($sound)) {
?>
				<object type="application/x-shockwave-flash" data="fadein.swf?src=<?php echo $sound;?>&autostart=yes&streaming=yes" width="100" height="30">
					<param name="movie" value="fadein.swf?src=<?php echo $sound;?>&autostart=yes&streaming=yes" />
					<param name="quality" value="high" /> 
					<param name="bgcolor" value="#C4D7DF" /> 
					<EMBED src="<?php echo $sound;?>" autostart=true loop=1 volume=100 hidden=true><NOEMBED><BGSOUND src="<?php echo $sound;?>"></NOEMBED> 
				</object> 
<?php 
}				 
?> 
				</td> 
			</tr> 
			<tr> 
				<td class="fadein" ID="test2"> 
<?php 
					if ( ($anzahl == 1) AND ($anzahl_n == 0) ) { 
?> 
						<a href="index.php?sid=<?php echo $sid;?>&action=sitterliste">Es ist ein Auftrag offen!</a>
<?php 
					}	
?> 
<?php 
					if ( ($anzahl == 1) AND ($anzahl_n == 1) ) { 
?> 
						<a href="index.php?sid=<?php echo $sid;?>&action=sitterliste"><b class="doc_red">Es ist ein Forschungsauftrag offen!</b></a>
<?php 
					}
?> 
<?php 
					if ( ($anzahl > 1) AND ($anzahl_n == 0) ) { 
?> 
						<a href="index.php?sid=<?php echo $sid;?>&action=sitterliste">Es sind <?php echo $anzahl;?> Aufträge offen!</a>
<?php 
					}
?> 
<?php 
					if ( ($anzahl > 1) AND ($anzahl_n >= 1) ) { 
?> 
						<a href="index.php?sid=<?php echo $sid;?>&action=sitterliste"><span class="doc_red">Es sind <?php echo $anzahl;?> Aufträge offen!</span></a>
<?php 
					}
?> 
				</td>
			</tr>
		</tbody>
	</table><br>

</div>

<?php
			}
		}
	}
}
?>