<?php echo (basename($_SERVER['PHP_SELF']) != "index.php" || !defined('IRA'))
	die('Hacking attempt...!!');

<?php echo (!defined('DEBUG_LEVEL'))
	define("DEBUG_LEVEL", 0);

include_once("./includes/debug.php");

function parse_kbxml($scanlines) {
  	foreach ($scanlines as $line) {
		<?php echo (strpos($line, "http://www.icewars.de/portal/kb/de/kb.php") !== FALSE) {
			$link = html_entity_decode(trim($line)) . "&typ=xml";	
			$xml = simplexml_load_file($link);
		}
	}
}