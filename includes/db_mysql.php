<?php
/*****************************************************************************/
/* db_mysql.php                                                              */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

class db {
	var $db_link_id;
	var $query_result;
	var $query_count;
	var $db_version;
	var $db_queries;

	function db_connect($host = 'localhost', $user = 'iwdb', $password = 'iwdb', $database = 'iwdb', $persistency = TRUE)
	{
		if ( $persistency )
		{
			$this->db_link_id = @mysql_pconnect($host, $user, $password);
		}
		else
		{
			$this->db_link_id = @mysql_connect($host, $user, $password);
		}

		$this->query_count = 0;
		$this->db_version = @mysql_get_server_info();
		$this->db_queries = "";
        mysql_set_charset('utf8', $this->db_link_id);
        return ( $this->db_link_id ) ? ( ( $this->db_select($database) ) ? $this->db_link_id: FALSE): FALSE;

	}

	function db_disconnect()
	{
		if ( $this->db_link_id )
		{
			if( $this->query_result )
			{
				$this->db_free_result($this->query_result);
			}

			return @mysql_close($this->db_link_id);
		} else {
            return FALSE;
        }
	}

	function db_select($database = 'iwdb')
	{
		return @mysql_select_db($database);
	}

	function db_query($query)
	{
		unset($this->query_result);
		if( $query != '' )
    {
			$this->query_result = @mysql_query($query, $this->db_link_id);
  		$this->query_count++;

		  if(!empty($this->db_queries))
			  $this->db_queries .= "<br\n>";
				
		  $this->db_queries .= $query;
						 
			return $this->query_result;
		}
		else
		{
			return FALSE;
		}
	}

	function db_num_rows($query_id = 0) {
		if( ! $query_id )
		{
			$query_id = $this->query_result;
		}

		if( $query_id )
		{
			$result = @mysql_num_rows($query_id);
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function db_fetch_row($query_id = 0) {
		if( ! $query_id )
		{
			$query_id = $this->query_result;
		}

		if( $query_id )
		{
			$result = @mysql_fetch_row($query_id);
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function db_fetch_array($query_id = 0) {
		if( ! $query_id )
		{
			$query_id = $this->query_result;
		}

		if( $query_id )
		{
			$result = @mysql_fetch_array($query_id, MYSQL_ASSOC);
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function db_fetch_object($query_id) {
		if( ! $query_id )
		{
			$query_id = $this->query_result;
		}

		if( $query_id )
		{
			$result = @mysql_fetch_object($query_id);
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function db_free_result($result) {
		return @mysql_free_result($result);
	}

	function db_error() {
		if ( ! $this->db_link_id )
		{
			$result['code'] = @mysql_errno();
			$result['msg'] = @mysql_error();
		}
		else
		{
			$result['code'] = @mysql_errno($this->db_link_id);
			$result['msg'] = @mysql_error($this->db_link_id);
		}

		return $result;
	}
}
?>