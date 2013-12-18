<?php

//verschiedene Sicherheitsfunktionen

/**
 * generate Entropy
 *
 * Tries a bunch of methods to get entropy in order
 * of preference and returns as soon as it has something.
 *
 * from http://seld.be/notes/unpredictable-hashes-for-humans
 *
 * modified by masel <masel789@gmail.com>
 *
 * @copyright Jordi Boggiano <j.boggiano@seld.be>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 *
 * @throws Exception
 * @return string entropy
 */
function generateEntropy()
{
    // try mcrypt
    if (function_exists('mcrypt_create_iv')) {
        $entropy = mcrypt_create_iv(128, MCRYPT_DEV_URANDOM);
        if (strlen($entropy) === 128) {
            return $entropy;
        }
    }

    // otherwise try ssl
    if (function_exists('openssl_random_pseudo_bytes')) {
        $entropy = openssl_random_pseudo_bytes(128, $strong);
        // use only if strong algo used
        if ((strlen($entropy) === 128) AND ($strong === true)) {
            return $entropy;
        }
    }

    // otherwise try to read from the unix RNG
    if (is_readable('/dev/urandom')) {
        $entropy = @file_get_contents('/dev/urandom', false, null, -1, 128);
        if (strlen($entropy) === 128) {
            return $entropy;
        }
    }

    throw new Exception('generating entrophy failed!');
}

/**
 * Grabs entropy and hashes it to normalize the output
 *
 * @copyright masel <masel789@gmail.com>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 *
 * @param int    $length   optional length of randomstring (default 30)
 * @param string $encoding optional encoding of randomstring hex, raw or base64 (default base64)
 *
 * @throws Exception
 * @return string randomstring
 */
function getRandomString($length = 30, $encoding = 'base64')
{
    $strRandomstring = '';

    $iLength = (int)$length;
    if ($iLength < 1) {
        $iLength = 30;
    }

    $strEntropy = generateEntropy();

    $strHash = hash('sha512', $strEntropy, true);              //get hash in raw-format
    if (!empty($strHash)) {
        $strHash = str_shuffle($strHash);                      //a finally shuffle just to be sure
        if (!empty($strHash)) {
            if (strtolower($encoding) === 'raw') {
                $strRandomstring .= $strHash;                  //get raw format
            } elseif (strtolower($encoding) === 'base64') {
                $strRandomstring .= base64_encode($strHash);   //get base64 format
            } else {
                $strRandomstring .= bin2hex($strHash);         //get hex format
            }
        }
    }

    if (!empty($strRandomstring)) {
        while (strlen($strRandomstring) < $iLength) {
            $strRandomstring .= getRandomString(64, $encoding);
        }

        if (strlen($strRandomstring) === $iLength) {
            return $strRandomstring;
        } elseif (strlen($strRandomstring) > $iLength) {
            return substr($strRandomstring, 0, $iLength);
        }
    } else {
        throw new Exception('generating randomstring failed!');
    }

}

//******************************************************************************
//
// Funktion für Zufallsstring mit Zeichen $values und Länge $length
// veraltet getRandomString() nutzen
function randomstring($values = '', $length = 30)
{
    return getRandomString($length, $encoding = 'base64');
}

//******************************************************************************
//
// auf sicheres Passwort überprüfen
function secure_password($password)
{
    $alert    = "";
    $password = trim($password);
    if (strlen($password) < 7) {
        $alert = "Passwort ist zu kurz (mindestens 7 Zeichen).";
    }
    if (!preg_match('%^.*([^a-zA-Z0-9]|[0-9])+.*([^a-zA-Z0-9]|[0-9])+.*$%', $password)) {
        $alert = "Passwort enthält nicht mindestens 2 Sonderzeichen oder Zahlen.";
    } else {
        if (!preg_match('%^.*[a-zA-Z]+.*[a-zA-Z]+.*$%', $password)) {
            $alert = "Passwort enthält nicht mindestens 2 Buchstaben.";
        }
    }

    return $alert;
}

//******************************************************************************
//
// userpasswort ändern
function changePassword($id, $password)
{
    global $db, $db_tb_user;

    $id = trim($id);

    if (empty($id)) {
        throw new Exception('empty id given');
    } elseif (empty($password)) {
        throw new Exception('empty password given');
    }

    $id = $db->escape($id);
    $password_hash = password_hash($password, HASHING_ALGO, array("cost" => HASHING_COST));

    $SQLdata = array (
        'password' => $password_hash
    );
    $db->db_update($db_tb_user, $SQLdata, "WHERE `id` = '" . $id . "'");

    $sql = "SELECT `password` FROM `{$db_tb_user}` WHERE `id` = '" . $id . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query user password.', '', __FILE__, __LINE__, $sql);
    $userdata = $db->db_fetch_array($result);
    if ($userdata['password'] !== $password_hash) {
        throw new Exception('writing new password failed!');
    }

}

function encrypt($data, $key) {
    //using AES (Rijndael 128bit Blocksize) with mcrypt Extension
    //Cipher Block Chaining Mode
    //256 bit Keylength

    $td = @mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    if ($td !== false) {

        $selftest = mcrypt_enc_self_test($td);                      //note: if self test is ok return is FALSE not TRUE
        if ($selftest === false) {

            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
            if (strlen($iv) !== mcrypt_enc_get_iv_size($td)) {
                throw new Exception('mcrypt create iv failed!');
            }

            $keyhash = hash('sha256', $key, true);                   //we hash the key here to accept all possible keyinputs, outputsize also matches desired keysize
            if (strlen($keyhash) !== 32) {
                throw new Exception('keyhash failed!');
            }

            $mcrypt_init = @mcrypt_generic_init($td, $keyhash, $iv);
            if (($mcrypt_init < 0) OR ($mcrypt_init === false)) {
                throw new Exception('mcrypt init failed!');
            }

            $encrypted_data = mcrypt_generic($td, $data);

            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            $encrypted_iv_data_base64 = base64_encode($iv . $encrypted_data);

            return $encrypted_iv_data_base64;

        } else {
            throw new Exception('mcrypt selftest failed!');
        }

    } else {
        throw new Exception('error mcrypt module open');
    }
}

function decrypt($encrypted_iv_data_base64, $key) {
    //using AES (Rijndael 128bit Blocksize) with mcrypt Extension
    //Cipher Block Chaining Mode
    //256 bit Keylength

    $td = @mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    if ($td !== false) {

        $selftest = mcrypt_enc_self_test($td);                      //note: if self test ok return is FALSE not TRUE
        if ($selftest === false) {

            $encrypted_iv_data = base64_decode($encrypted_iv_data_base64);
            $iv_size = mcrypt_enc_get_iv_size($td);
            $iv = substr($encrypted_iv_data, 0, $iv_size);
            $encrypted_data = substr($encrypted_iv_data, $iv_size);

            $keyhash = hash('sha256', $key, true);                   //we hash the key here to accept all possible keyinputs, outputsize also matches desired keysize
            if (strlen($keyhash) !== 32) {
                throw new Exception('keyhash failed!');
            }

            $mcrypt_init = @mcrypt_generic_init($td, $keyhash, $iv);
            if (($mcrypt_init < 0) OR ($mcrypt_init === false)) {
                throw new Exception('mcrypt init failed!');
            }

            $decrypted_data = rtrim(mdecrypt_generic($td , $encrypted_data), "\0");

            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);

            return $decrypted_data;

        } else {
            throw new Exception('mcrypt selftest failed!');
        }

    } else {
        throw new Exception('error mcrypt module open');
    }
}

function getRemoteIP()
{
    foreach (array('REMOTE_ADDR', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                $filteredRemoteIP = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                if (!empty($filteredRemoteIP)) {
                    return $filteredRemoteIP;
                }
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'];     //use standard ip and log this maybe
}

function blockedIP($ip) {
    global $db, $db_tb_wronglogin, $config_wronglogin_timeout, $config_wronglogins;

    // zu alte falsche Logins löschen
    $sql = "DELETE FROM `{$db_tb_wronglogin}` WHERE `date`<" . (CURRENT_UNIX_TIME - $config_wronglogin_timeout);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not delete wrong login information.', '', __FILE__, __LINE__, $sql);

    $sql = "SELECT count(*) as numWrongLogins FROM `{$db_tb_wronglogin}` WHERE `ip`='" . $ip . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    if ($row['numWrongLogins'] >=$config_wronglogins) {
        return true;
    } else {
        return false;
    }
}
