<?php
//
// +---------------------------------------------------------------------+
// | phpOpenTracker - The Website Traffic and Visitor Analysis Solution  |
// +---------------------------------------------------------------------+
// | Copyright (c) 2000-2003 Sebastian Bergmann. All rights reserved.    |
// +---------------------------------------------------------------------+
// | This source file is subject to the phpOpenTracker Software License, |
// | Version 1.0, that is bundled with this package in the file LICENSE. |
// | If you did not receive a copy of this file, you may either read the |
// | license online at http://phpOpenTracker.de/license/1_0.txt, or send |
// | a note to license@phpOpenTracker.de, so we can mail you a copy.     |
// +---------------------------------------------------------------------+
// | Author: Christopher Hughes <christopher.hughes@cancom.com>          |
// +---------------------------------------------------------------------+
//
// $Id: mssql.php,v 1.12 2003/02/09 07:14:48 bergmann Exp $
//

/**
* phpOpenTracker MS SQL Server Database Handler
*
* @author   Christopher Hughes <christopher.hughes@cancom.com>
* @version  $Revision: 1.12 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_DB_mssql extends phpOpenTracker_DB {
  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_DB_mssql() {
    $this->phpOpenTracker_DB();

    $this->connection = @mssql_connect(
      $this->config['db_host'],
      $this->config['db_user'],
      $this->config['db_password']
    );

    if (!$this->connection) {
      phpOpenTracker::handleError(
        'Could not connect to database.',
        E_USER_ERROR
      );
    }

    if (!@mssql_select_db($this->config['db_database'], $this->connection)) {
      phpOpenTracker::handleError(
        'Could not select database.',
        E_USER_ERROR
      );
    }
  }

  /**
  * Fetches a row from the current result set.
  *
  * @access public
  * @return array
  */
  function fetchRow() {
    if (is_resource($this->result)) {
      $row = @mssql_fetch_assoc($this->result);

      if (is_array($row)) {
        return $row;
      }
    }

    return false;
  }

  /**
  * Performs an SQL query.
  *
  * @param  string           $query
  * @param  optional mixed   $limit
  * @param  optional boolean $warnOnFailure
  * @access public
  */
  function query($query, $limit = false, $warnOnFailure = true) {
    if ($limit != false) {
      $query = str_replace(
        'SELECT',
        'SELECT TOP ' . $limit,
        $query
      );
    }

    if ($this->config['debug_level'] > 1) {
      $this->debugQuery($query);
    }

    if (isset($this->result) && is_resource($this->result)) {
      @mssql_free_result($this->result);
    }

    $this->result = @mssql_query($query, $this->connection);

    if (!$this->result && $warnOnFailure) {
      phpOpenTracker::handleError(
        'Database query failed.',
        E_USER_ERROR
      );
    }
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
