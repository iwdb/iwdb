<?php
/*****************************************************************************
 * db_mysql.php                                                              *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

class db
{
    var $db_link_id;
    var $query_result;
    var $query_count;
    var $db_version;
    var $db_queries;

    function db_connect($host = 'localhost', $user = 'iwdb', $password = 'iwdb', $database = 'iwdb', $persistency = true)
    {
        if ($persistency) {
            $this->db_link_id = @mysql_pconnect($host, $user, $password);
        } else {
            $this->db_link_id = @mysql_connect($host, $user, $password);
        }

        if ($this->db_link_id !== false) {

            $this->query_count = 0;
            $this->db_version  = @mysql_get_server_info();
            $this->db_queries  = array();
            $this->db_select($database);

            mysql_set_charset('utf8', $this->db_link_id);
            return $this->db_link_id;

        } else {
            return false;
        }

    }

    function db_disconnect()
    {
        if ($this->db_link_id) {
            if ($this->query_result) {
                $this->db_free_result($this->query_result);
            }

            return @mysql_close($this->db_link_id);
        } else {
            return false;
        }
    }

    function db_select($database)
    {
        return @mysql_select_db($database);
    }

    function escape($string)
    {
        return mysql_real_escape_string($string, $this->db_link_id);
    }

    function db_query($query)
    {
        unset($this->query_result);
        if (!empty($query)) {
            //log queries if logging enabled
            if (defined('IWDB_LOG_DB_QUERIES') AND (IWDB_LOG_DB_QUERIES === true)) {
                $this->db_queries[] = $query;
            }

            $this->query_result = @mysql_query($query, $this->db_link_id);
            if ($this->query_result == false) {
                trigger_error("Database query error: " . @mysql_error($this->db_link_id), E_USER_ERROR);
            }

            $this->query_count++;

            return $this->query_result;
        } else {
            return false;
        }
    }

    /**
     * function db_insert
     *
     * Fügt die Daten des übergebenen Arrays in die Datenbank ein.
     *
     * @param string $table Tabellenbezeichner
     * @param array  $data  Daten
     *
     * @throws Exception
     * @return resource|bool Queryhandle bei Erfolg, boolean false bei Fehler
     *
     * @author masel
     */
    function db_insert($table, $data)
    {
        unset($this->query_result);

        if (empty($table) OR empty($data) OR !is_array($data)) {
            return false;
        }

        $query = "INSERT INTO `" . $table . "` (";

        //sql-query zusammenbauen
        foreach ($data as $key => $value) {

            if ($value === null) {
                $data[$key] = "NULL";
            } elseif ($value === false) { //boolean ist meist tinyint(1)
                $data[$key] = "0";
            } elseif ($value === true) {
                $data[$key] = "1";
            } elseif (is_string($value)) { //Wert ist String? -> escapen
                $value = mysql_real_escape_string($value, $this->db_link_id);
                if ($value === false) {
                    throw new Exception('Value escaping failed!');
                }
                $data[$key] = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)){
                throw new Exception('Invalid value!');
            }

        }

        $query .= '`' . implode(array_keys($data), "`,`");
        $query .= "`) VALUES (";
        $query .= implode($data, ",");
        $query .= ");";

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    /**
     * function db_insert_multiple
     *
     * Fügt die Daten des übergebenen Arrays in die Datenbank ein.
     *
     * @param string $table       Tabellenbezeichner
     * @param array  $columnnames Spaltenbezeichner
     * @param array  $data        Daten
     *
     * @throws Exception
     * @return resource|bool Queryhandle bei Erfolg, boolean false bei Fehler
     *
     * @author masel
     */
    function db_insert_multiple($table, $columnnames, $data)
    {
        unset($this->query_result);

        if (empty($table) OR empty($columnnames) OR !is_array($columnnames) OR empty($data) OR !is_array($data)) {
            return false;
        }

        $query = "INSERT INTO `" . $table . "` (";

        //sql-query zusammenbauen

        $query .= '`' . implode($columnnames, "`,`");
        $query .= "`) VALUES";

        foreach ($data as $datarow) {
            foreach ($datarow as $key => $value) {

                if ($value === null) {
                    $datarow[$key] = "NULL";
                } elseif ($value === false) { //boolean ist meist tinyint(1)
                    $datarow[$key] = "0";
                } elseif ($value === true) {
                    $datarow[$key] = "1";
                } elseif (is_string($value)) { //Wert ist String? -> escapen
                    $value = mysql_real_escape_string($value, $this->db_link_id);
                    if ($value === false) {
                        throw new Exception('Value escaping failed!');
                    }
                    $datarow[$key] = "'$value'";
                } elseif (!is_int($value) AND !is_float($value)){
                    throw new Exception('Invalid values!');
                }

            }

            $query .= " (";
            $query .= implode($datarow, ",");
            $query .= "),";
        }

        $query = mb_substr($query, 0, -1) . ';';

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    /**
     * function db_update
     *
     * Aktualisiert DB-Eintrag mit den übergebenen Daten.
     *
     * @param string $table         Tabellenbezeichner
     * @param array  $data          Daten
     * @param string $additionalSQL Zusätzliche sql Anweisungen
     *
     * @throws Exception
     * @return bool|resource Queryhandle bei Erfolg, false bei Fehler
     *
     * @author   masel
     */
    function db_update($table, $data, $additionalSQL = '')
    {
        unset($this->query_result);

        if (empty($table)) {
            throw new Exception('invalid table!');
        } elseif (empty($data) OR !is_array($data)) {
            throw new Exception('invalid data!');
        }

        $query = "Update `" . $table . "` SET ";

        $datapairs = Array();

        //sql-query zusammenbauen
        foreach ($data as $key => $value) {

            if ($value === null) {
                $value = "NULL";
            } elseif ($value === false) { //boolean ist meist tinyint(1)
                $value = "0";
            } elseif ($value === true) {
                $value = "1";
            } elseif (is_string($value)) { //Wert ist String? -> escapen
                $value = mysql_real_escape_string($value, $this->db_link_id);
                if ($value === false) {
                    throw new Exception('Value escaping failed!');
                }
                $value = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)){
                throw new Exception('Invalid values!');
            }
            $datapairs[] = "`$key` = $value";
        }
        $query .= implode(', ', $datapairs);


        if ($additionalSQL !== '') {
            $query .= ' ' . $additionalSQL;
        }

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    /**
     * function db_insertupdate
     *
     * Fügt die Daten des übergebenen Arrays in die Datenbank ein
     * oder updatet sie falls vorhanden und mit einzigartigem Index versehen
     *
     * @param string $table Tabellenbezeichner
     * @param array  $data  Daten
     *
     * @throws Exception
     * @return bool|resource Queryhandle bei Erfolg, false bei Fehler
     *
     * @author masel
     */
    function db_insertupdate($table, $data)
    {
        unset($this->query_result);

        if (empty($table) OR empty($data) OR !is_array($data)) {
            return false;
        }

        $query = "INSERT INTO `" . $table . "` (";

        //INSERT-Teil zusammenbauen
        foreach ($data as $key => $value) {

            if ($value === null) {
                $data[$key] = "NULL";
            } elseif ($value === false) { //boolean ist meist tinyint(1)
                $data[$key] = "0";
            } elseif ($value === true) {
                $data[$key] = "1";
            } elseif (is_string($value)) { //Wert ist String? -> escapen
                $value = mysql_real_escape_string($value, $this->db_link_id);
                if ($value === false) {
                    throw new Exception('Value escaping failed!');
                }
                $data[$key] = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)) {
                throw new Exception('Invalid values!');
            }

        }

        $query .= '`' . implode(array_keys($data), "`,`");
        $query .= "`) VALUES (";
        $query .= implode($data, ",");
        $query .= ") ON DUPLICATE KEY UPDATE ";

        $datapairs = Array();

        //UPDATE-Teil zusammenbauen
        foreach ($data as $key => $value) {

            //daten sind bereits escaped

            $datapairs[] = "`$key` = $value";
        }

        $query .= implode(', ', $datapairs);
        $query .= ';';

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    /**
     * function db_insertignore
     *
     * Fügt die Daten des übergebenen Arrays in die Datenbank ein
     * oder ignoriert die Eingabe falls schon vorhanden
     *
     * @param string $table Tabellenbezeichner
     * @param array  $data  Daten
     *
     * @throws Exception
     * @return bool|resource Queryhandle bei Erfolg, false bei Fehler
     *
     * @author masel
     */
    function db_insertignore($table, $data)
    {
        unset($this->query_result);

        if (empty($table) OR empty($data) OR !is_array($data)) {
            return false;
        }

        $query = "INSERT INTO `" . $table . "` (";

        //INSERT-Teil zusammenbauen
        foreach ($data as $key => $value) {

            if ($value === null) {
                $data[$key] = "NULL";
            } elseif ($value === false) { //boolean ist meist tinyint(1)
                $data[$key] = "0";
            } elseif ($value === true) {
                $data[$key] = "1";
            } elseif (is_string($value)) { //Wert ist String? -> escapen
                $value = mysql_real_escape_string($value, $this->db_link_id);
                if ($value === false) {
                    throw new Exception('Value escaping failed!');
                }
                $data[$key] = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)){
                throw new Exception('Invalid values!');
            }

        }

        $query .= '`' . implode(array_keys($data), "`,`");
        $query .= "`) VALUES (";
        $query .= implode($data, ",");
        $query .= ") ON DUPLICATE KEY UPDATE `".key($data)."`=`".key($data)."`;";   //means do nothing

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    function db_insert_id()
    {
        return mysql_insert_id($this->db_link_id);
    }

    function db_num_rows($query_id)
    {
        if (empty($query_id)) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            return @mysql_num_rows($query_id);
        } else {
            return false;
        }
    }

    function db_fetch_row($query_id)
    {
        if (empty($query_id)) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            return @mysql_fetch_row($query_id);
        } else {
            return false;
        }
    }

    function db_fetch_array($query_id)
    {
        if (empty($query_id)) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            return @mysql_fetch_array($query_id, MYSQL_ASSOC);
        } else {
            return false;
        }
    }

    function db_fetch_object($query_id)
    {
        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            $result = @mysql_fetch_object($query_id);

            return $result;
        } else {
            return false;
        }
    }

    function db_free_result($result)
    {
        return @mysql_free_result($result);
    }

    function db_error()
    {
        if (!$this->db_link_id) {
            $result['code'] = @mysql_errno();
            $result['msg']  = @mysql_error();
        } else {
            $result['code'] = @mysql_errno($this->db_link_id);
            $result['msg']  = @mysql_error($this->db_link_id);
        }

        return $result;
    }
}