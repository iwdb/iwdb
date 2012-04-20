if (basename($_SERVER['PHP_SELF']) != "index.php" || !defined('IRA'))
	die('Hacking attempt...!!');

if (!defined('DEBUG_LEVEL'))
	define("DEBUG_LEVEL", 0);

include_once("./includes/debug.php");

function parse_kbxml($scanlines) {
  	foreach ($scanlines as $line) {
		if (strpos($line, "http://www.icewars.de/portal/kb/de/kb.php") !== FALSE) {
			$link = html_entity_decode(trim($line)) . "&typ=xml";	
			$xml = simplexml_load_file($link);
		}
	}
}
