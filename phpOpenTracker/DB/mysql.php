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
// $Id: mysql.php,v 1.17 2003/02/11 07:09:43 bergmann Exp $
//

/**
* phpOpenTracker MySQL Database Handler
*
* @author   Sebastian Bergmann <sebastian@phpOpenTracker.de>
* @version  $Revision: 1.17 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_DB_mysql extends phpOpenTracker_DB {
  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_DB_mysql() {
    $this->phpOpenTracker_DB();

    $connectionString = sprintf(
      '%s:%s',

      $this->config['db_host'],
      (($this->config['db_port'] == 'default') ? '3306' : $this->config['db_port'])
    );

    if ($this->config['db_socket'] != 'default') {
      $connectionString .= ':' . $this->config['db_socket'];
    }

    $this->connection = @mysql_connect(
      $connectionString,
      $this->config['db_user'],
      $this->config['db_password']
    );
    if (!$this->connection ||
        !@mysql_select_db($this->config['db_database'], $this->connection)) {
      return phpOpenTracker::handleError(
        'Could not connect to database.',
        E_USER_ERROR
      );
    }
  }

  /**
  * Prints debug information for an SQL query.
  *
  * @param  string  $query
  * @access public
  */
  function debugQuery($query) {
    if ($explainQuery = stristr($query, 'SELECT')) {
      $result = @mysql_query('EXPLAIN ' . $explainQuery, $this->connection);

      while ($row = @mysql_fetch_assoc($result)) {
        $explain[] = $row;
      }
    }

    $debugQuery = explode("\n", $query);

    for ($i = 0; $i < sizeof($debugQuery); $i++) {
      $debugQuery[$i] = trim($debugQuery[$i]);
    }

    $debugQuery = implode("\n", $debugQuery);

    printf(
      '<table border="1" width="100%%"><tr><td valign="top">%d</td><td valign="top" colspan="%d"><pre>%s</pre></td></tr>',

      ++$this->numQueries,
      isset($explain[0]) ? sizeof($explain[0]) : 1,
      $debugQuery
    );

    if (isset($explain)) {
      foreach ($explain as $row) {
        echo '<tr><td>&nbsp;</td>';

        if (isset($row['Comment'])) {
          echo '<td>' . $row['Comment'] . '</td>';
        } else {
          foreach ($row as $field => $value) {
            printf(
              '<td valign="top">%s:<br /><nobr>%s</nobr></td>',

              $field,
              $value
            );
          }
        }

        echo '</tr>';
      }
    }

    echo '</table>';
  }

  /**
  * Fetches a row from the current result set.
  *
  * @access public
  * @return array
  */
  function fetchRow() {
    if (is_resource($this->result)) {
      $row = @mysql_fetch_assoc($this->result);

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
      $query .= ' LIMIT ' . $limit;
    }

    if ($this->config['debug_level'] > 1) {
      $this->debugQuery($query);
    }

    if (isset($this->result) && is_resource($this->result)) {
      @mysql_free_result($this->result);
    }

    $this->result = @mysql_unbuffered_query($query, $this->connection);

    if (!$this->result && $warnOnFailure) {
      phpOpenTracker::handleError(
        @mysql_error($this->connection),
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
    $string = substr($string, 0, 254);

    if (function_exists('mysql_real_escape_string')) {
      return mysql_real_escape_string($string, $this->connection);
    } else {
      return mysql_escape_string($string);
    }
  }

  /**
  * Returns TRUE if the database supports nested queries
  * and FALSE otherwise.
  *
  * @return boolean
  * @access public
  * @since  phpOpenTracker 1.1.0
  */
  function supportsNestedQueries() {
    if (substr(mysql_get_server_info($this->connection), 0, 3) >= 4.1) {
      return true;
    }

    return false;
  }
}

//
// "phpOpenTracker essenya, gul meletya;
//  Sebastian carneron PHP."
//
?>
