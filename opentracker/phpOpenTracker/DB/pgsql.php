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
// | Author: Sebastian Bergmann <sebastian@phpOpenTracker.de>            |
// +---------------------------------------------------------------------+
//
// $Id: pgsql.php,v 1.12.2.1 2003/03/28 05:41:04 bergmann Exp $
//

/**
* phpOpenTracker PostgreSQL Database Handler
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.12.2.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_DB_pgsql extends phpOpenTracker_DB {
  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_DB_pgsql() {
    $this->phpOpenTracker_DB();

    $connectionString = sprintf(
      "%s %s dbname=%s user=%s password=%s",
      ($this->config['db_host'] == 'socket')  ? '' : 'host=' . $this->config['db_host'],
      ($this->config['db_port'] == 'default') ? '' : 'port=' . $this->config['db_port'],
      $this->config['db_database'],
      $this->config['db_user'],
      $this->config['db_password']
    );

    if (!$this->connection = @pg_connect($connectionString)) {
      return phpOpenTracker::handleError(
        'Could not connect to database.',
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
      return @pg_fetch_array($this->result);
    } else {
      return false;
    }
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
      $query .= ' LIMIT ' . $limit;
    }

    if ($this->config['debug_level'] > 1) {
      $this->debugQuery($query);
    }

    if (isset($this->result) && is_resource($this->result)) {
      @pg_freeresult($this->result);
    }

    $this->result = @pg_exec($this->connection, $query);

    if (!$this->result && $warnOnFailure) {
      phpOpenTracker::handleError(
        @pg_errormessage($this->connection),
        E_USER_ERROR
      );
    }
  }

  /**
  * Prepares a string for an SQL query.
  *
  * @param  string $string
  * @return string
  * @access public
  */
  function prepareString($string) {
    return str_replace(
      array("'",  '\\'),
      array("''", '\\\\'),
      substr($string, 0, 254)
    );
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
