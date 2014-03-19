<?php
/*****************************************************************************
 * db_mysqli.php                                                              *
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

    function db_connect($host, $user, $password, $database, $persistency = false)
    {
        if ($persistency) {
            $this->db_link_id = @mysqli_connect('p:'.$host, $user, $password, $database);
        } else {
            $this->db_link_id = @mysqli_connect($host, $user, $password, $database);
        }

        if ($this->db_link_id !== false) {

            $this->query_count = 0;
            $this->db_version  = @mysqli_get_server_info($this->db_link_id);
            $this->db_queries  = array();

            if (!mysqli_set_charset($this->db_link_id, 'utf8')) {
                trigger_error('Database error: unable to load character set utf8' , E_USER_ERROR);
            }

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

            return @mysqli_close($this->db_link_id);
        } else {
            return false;
        }
    }

    function escape($string)
    {
        return mysqli_real_escape_string($this->db_link_id, $string);
    }

    function db_query($query)
    {
        unset($this->query_result);
        if (!empty($query)) {
            //log queries if logging enabled
            if (defined('IWDB_LOG_DB_QUERIES') AND (IWDB_LOG_DB_QUERIES === true)) {
                $this->db_queries[] = $query;
            }

            $this->query_result = @mysqli_query($this->db_link_id, $query);
            if ($this->query_result == false) {
                trigger_error("Database query error: " . mysqli_error($this->db_link_id) . ' (' . mysqli_errno($this->db_link_id) . ')', E_USER_ERROR);
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
                $value = mysqli_real_escape_string($this->db_link_id, $value);
                if ($value === false) {
                    trigger_error('Value escaping failed!', E_USER_ERROR);
                }
                $data[$key] = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)){
                trigger_error('Invalid value!', E_USER_ERROR);
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
                    $value = mysqli_real_escape_string($this->db_link_id, $value);
                    if ($value === false) {
                        trigger_error('Value escaping failed!', E_USER_ERROR);
                    }
                    $datarow[$key] = "'$value'";
                } elseif (!is_int($value) AND !is_float($value)){
                    trigger_error('Invalid values!', E_USER_ERROR);
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
     * @return bool|resource Queryhandle bei Erfolg, false bei Fehler
     *
     * @author   masel
     */
    function db_update($table, $data, $additionalSQL = '')
    {
        unset($this->query_result);

        if (empty($table)) {
            trigger_error('invalid db-table!', E_USER_ERROR);
        } elseif (empty($data) OR !is_array($data)) {
            trigger_error('invalid data!', E_USER_ERROR);
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
                $value = mysqli_real_escape_string($this->db_link_id, $value);
                if ($value === false) {
                    trigger_error('Value escaping failed!', E_USER_ERROR);
                }
                $value = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)){
                trigger_error('Invalid values!', E_USER_ERROR);
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
                $value = mysqli_real_escape_string($this->db_link_id, $value);
                if ($value === false) {
                    trigger_error('Value escaping failed!', E_USER_ERROR);
                }
                $data[$key] = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)) {
                trigger_error('Invalid values!', E_USER_ERROR);
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
                $value = mysqli_real_escape_string($this->db_link_id, $value);
                if ($value === false) {
                    trigger_error('Value escaping failed!', E_USER_ERROR);
                }
                $data[$key] = "'$value'";
            } elseif (!is_int($value) AND !is_float($value)){
                trigger_error('Invalid values!', E_USER_ERROR);
            }

        }

        $query .= '`' . implode(array_keys($data), "`,`");
        $query .= "`) VALUES (";
        $query .= implode($data, ",");
        $query .= ") ON DUPLICATE KEY UPDATE `".key($data)."`=`".key($data)."`;";   //means do nothing

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    /**
     * function db_insertignore_multiple
     *
     * Fügt die Daten des übergebenen Arrays in die Datenbank ein
     * oder ignoriert die Eingabe falls schon vorhanden
     *
     * @param string $table       Tabellenbezeichner
     * @param array  $columnnames Spaltenbezeichner
     * @param array  $data        Daten
     *
     * @return resource|bool Queryhandle bei Erfolg, boolean false bei Fehler
     *
     * @author masel
     */
    function db_insertignore_multiple($table, $columnnames, $data)
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
                    $value = mysqli_real_escape_string($this->db_link_id, $value);
                    if ($value === false) {
                        trigger_error('Value escaping failed!', E_USER_ERROR);
                    }
                    $datarow[$key] = "'$value'";
                } elseif (!is_int($value) AND !is_float($value)){
                    trigger_error('Invalid values!', E_USER_ERROR);
                }

            }

            $query .= " (";
            $query .= implode($datarow, ",");
            $query .= "),";
        }

        $query = mb_substr($query, 0, -1);
        $query .= " ON DUPLICATE KEY UPDATE `".$columnnames[0]."`=`".$columnnames[0]."`;";   //means do nothing

        $this->query_result = $this->db_query($query, $this->db_link_id);

        return $this->query_result;

    }

    function db_insert_id()
    {
        return mysqli_insert_id($this->db_link_id);
    }

    function db_num_rows($query_id)
    {
        if (empty($query_id)) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            return mysqli_num_rows($query_id);
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
            return mysqli_fetch_row($query_id);
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
            return mysqli_fetch_array($query_id, MYSQLI_ASSOC);
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
            return mysqli_fetch_object($query_id);
        } else {
            return false;
        }
    }

    function db_free_result($result)
    {
        mysqli_free_result($result);
    }

    function db_errno()
    {
        return mysqli_errno($this->db_link_id);
    }

    function db_error()
    {
        return mysqli_error($this->db_link_id);
    }

    function db_error_ex()
    {
        $result['code'] = mysqli_errno($this->db_link_id);
        $result['msg']  = mysqli_error($this->db_link_id);

        return $result;
    }
}