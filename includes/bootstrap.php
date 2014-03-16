<?php
/**
 * bootstrapfile der iwdb <br>
 * for help see:
 *
 * @link       https://handels-gilde.org/?www/forum/index.php;board=1099.0 Entwicklerforum
 * @link       https://github.com/iwdb/iwdb github repo
 *
 * @author     masel <masel789@googlemail.com>
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU GPL version 2 or any later version
 * @package    iwdb
 * @subpackage Bootstrap
 */

if(!empty($_SERVER['SCRIPT_NAME']) AND basename($_SERVER['SCRIPT_NAME']) === 'ajax.php') {       //Ajax-Requests gehen nur zu dieser Datei
    define('AJAX_REQUEST', true);
} else {
    define('AJAX_REQUEST', false);
}

if (AJAX_REQUEST !== true) {
    //bcrypt hashing testen, sollte gehen ab php 5.3.7+ und zurückportierten (zB. Debian-) Versionen
    $properlyhash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
    $testhash = crypt("password", $properlyhash);
    if ($testhash  !== $properlyhash) {
        exit('bcrypt arbeitet mit dieser php-Version ('.PHP_VERSION.') nicht wie vorgesehen!');
    }

    if (!extension_loaded('mcrypt')) {
        exit('mcrypt erweiterung nicht vorhanden!');
    }
}

//all errors on
error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", '1');
libxml_use_internal_errors(true);
$error = '';

ini_set("pcre.recursion_limit", "524");             //php-Standardwert 100.000 ist viel zu hoch, 524 sollte auf allen Systemen laufen

//set some standards
date_default_timezone_set('Europe/Berlin');
mb_internal_encoding("UTF-8"); //just to make sure we are talking the same language
mb_http_output("UTF-8");
header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: SAMEORIGIN');              //IWDB nicht innerhalb von anderen Frames darstellen (Clickjacking protection)
header('X-XSS-Protection: 1; mode=block');          //Cross-site scripting (XSS) Schutz
header('X-UA-Compatible: IE=Edge,chrome=1');        //Google Chrome Frame im IE (falls vorhanden) oder ab IE9 neusten IE renderer (kein compability mode) nutzen

// Das aktuelle Datum wird pro Skriptaufruf nur einmal geholt, +-x kann
// entsprechend hier geändert werden
define("CURRENT_UNIX_TIME", time());

// Basisdefinitionen für Zeiträume.
define("MINUTE", 60);
define("HOUR", 60 * MINUTE);
define("DAY", 24 * HOUR);

// veraltetete Zeitdefinitionen
$config_date = CURRENT_UNIX_TIME;
$MINUTES = MINUTE;
$HOURS = HOUR;
$DAYS = DAY;

require_once './includes/errorHandler.php';         //Fehler handler laden
set_error_handler("errorHandler");

// some other constants
// ToDo: clean them up
define('DEBUG', true);
define('IWDB_LOG_DB_QUERIES', false);
define('IRA', true);
define('NEBULA', true);
define('ALLY_MEMBERS_ON_MAP', true);
define('GENERAL_ERROR', 'GENERAL_ERROR');
define("DB_MAX_INSERTS", 500);

define('SITTEN_DISABLED', 2);
define('SITTEN_ONLY_NEWTASKS', 0);
define('SITTEN_ONLY_LOGINS', 3);
define('SITTEN_BOTH', 1);

if (!defined('PASSWORD_BCRYPT')) {
    if (AJAX_REQUEST !== true) {
        require_once './includes/hashingapi.php';             //password hashing api compatibility for php < 5.5, nicht benötigt bei ajax requests

        //Passwort Hashing Einstellungen
        define('HASHING_ALGO', PASSWORD_DEFAULT);       // momentan wird bcrypt (= PASSWORD_BCRYPT) genutzt
        define('HASHING_COST', 10);                     //4-31 default 10, _nicht ändern_ außer du weist was du tust
    }
}

require_once './config/config.php';                 //IWDB Einstellungen
require_once './config/configsql.php';              //Datenbank Zugangsdaten
require_once './config/configally.php';             //Allianzeinstellungen
require_once './includes/dBug.php';                 //bessere Debugausgabe
require_once './includes/debug.php';                //Debug Funktionen
require_once './includes/function.php';             //sonstige Funktionen
require_once './includes/function_filter.php';      //Filter und Validierungsfunktionen
require_once './includes/function_parser.php';      //Parserhilfsfunktionen
require_once './includes/function_security.php';    //Sicherheitsfunktionen
require_once './includes/db_mysql.php';             //MySQL DB Klasse

define('REMOTE_IP', getRemoteIP());
if ($phpids_enabled) {
    require_once './includes/ids.php';              //einige Module sind ziemlich schlecht, besser wir testen die Inputs mit phpids
}

//DB Verbindung herstellen
$db = new db();
$link_id = $db->db_connect($db_host, $db_user, $db_pass, $db_name);
if ($link_id == false) {
    exit('Could not connect to database.');
}

// Tabellen mit IWDB-Prefix aus der DB lesen
$sql    = "SELECT `table_name` FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `table_schema` = '$db_name' AND `table_name` LIKE '$db_prefix%';";
$result = $db->db_query($sql);
if ($db->db_num_rows($result) == 0) {
    exit('<div style="text-align:center;color:red">Keine Datenbanktabellen gefunden!</div>');
}
while ($row = $db->db_fetch_array($result)) {
    $tbname    = "db_tb_" . mb_substr($row['table_name'], mb_strlen($db_prefix));
    ${$tbname} = $row['table_name'];
}

$action = preg_replace('/[^a-zA-Z0-9_-]/', '', mb_substr(getVar('action'), 0, 100)); //get and filter actionstring (limited to 100 chars)
if (empty($action)) {
    $action = $config_default_action;
}

require_once("./includes/sid.php");

$sql = "SELECT `gesperrt` FROM `{$db_tb_user}` WHERE `id` = '{$user_id}';";
$result_g = $db->db_query($sql);
$row_g = $db->db_fetch_array($result_g);
if ($row_g['gesperrt']) {
    die ('<div style="text-align:center;color:red">Dein Account ist gesperrt worden!</div>');
}