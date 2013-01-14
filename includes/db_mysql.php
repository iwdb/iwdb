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
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum/Repo wenden:                                              *
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

        $this->query_count = 0;
        $this->db_version  = @mysql_get_server_info();
        $this->db_queries  = "";
        mysql_set_charset('utf8', $this->db_link_id);

        return ($this->db_link_id) ? (($this->db_select($database)) ? $this->db_link_id : false) : false;

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

    function db_select($database = 'iwdb')
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
        if ($query != '') {
            $this->query_result = @mysql_query($query, $this->db_link_id);
            $this->query_count++;

            if (!empty($this->db_queries)) {
                $this->db_queries .= "<br>\n";
            }

            $this->db_queries .= $query;

            return $this->query_result;
        } else {
            return false;
        }
    }

    function db_update($table, $data, $add = '')
    {
        unset($this->query_result);

        if (empty($table)) {
            return false;
        } else {
            $query = "Update `" . $table . "` SET ";
        }

        if (empty($data)) {
            return false;
        }

        if (is_array($data)) {
            $updates = Array();

            //sql-query zusammenbauen
            foreach ($data as $key => $value) {

                if ($value === null) {
                    $value = "NULL";
                } elseif (is_string($value)) { //Wert ist String? -> escapen!
                    $value = mysql_real_escape_string($value, $this->db_link_id);
                    if ($value === false) {
                        return false;
                    }
                    $value = "'$value'";
                }
                $updates[] = "$key = $value";
            }
            $query .= implode(', ', $updates);

        } else {
            $query .= mysql_real_escape_string($data, $this->db_link_id);
        }

        if ($add !== '') {
            $query .= ' ' . $add;
        }

        $this->query_result = @mysql_query($query, $this->db_link_id);

        return $this->query_result;

    }

    function db_num_rows($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            $result = @mysql_num_rows($query_id);

            return $result;
        } else {
            return false;
        }
    }

    function db_fetch_row($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            $result = @mysql_fetch_row($query_id);

            return $result;
        } else {
            return false;
        }
    }

    function db_fetch_array($query_id = 0)
    {
        if (!$query_id) {
            $query_id = $this->query_result;
        }

        if ($query_id) {
            $result = @mysql_fetch_array($query_id, MYSQL_ASSOC);

            return $result;
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
