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
// | Author: Thomas Fromm <thomas@phpOpenTracker.de>                     |
// +---------------------------------------------------------------------+
//
// $Id: oci8.php,v 1.11.2.1 2003/03/12 17:33:09 bergmann Exp $
//

/**
* phpOpenTracker Oracle Database Handler
*
* @author   Thomas Fromm <thomas@phpOpenTracker.de>
* @version  $Revision: 1.11.2.1 $
* @since    phpOpenTracker 1.0.0
*/
class phpOpenTracker_DB_oci8 extends phpOpenTracker_DB {
  /**
  * Constructor.
  *
  * @access public
  */
  function phpOpenTracker_DB_oci8() {
    $this->phpOpenTracker_DB();

    $database = ($this->config['db_database'] == 'default') ? '' : $this->config['db_database'];

    $this->connection = @ocilogon(
      $this->config['db_user'],
      $this->config['db_password'],
      $database
    );

    if (!$this->connection) {
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
      if (@OCIFetchInto($this->result, $result, OCI_ASSOC)) {
        return array_change_key_case($result, CASE_LOWER);
      }
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
      $query = sprintf(
        'SELECT * FROM (%s) WHERE ROWNUM <= %d',

        $query,
        $limit
      );
    }

    if ($this->config['debug_level'] > 1) {
      $this->debugQuery($query);
    }

    if (isset($this->result) && is_resource($this->result)) {
      @OCIFreeStatement($this->result);
    }

    $this->result = @OCIParse($this->connection, $query);

    if (!$this->result) {
      $error = OCIError($this->result);

      phpOpenTracker::handleError(
        $error['code'] . $error['message'],
        E_USER_ERROR
      );
    }

    @OCIExecute($this->result);

    if (!$this->result && $warnOnFailure) {
      $error = OCIError($this->result);

      phpOpenTracker::handleError(
        $error['code'] . $error['message'],
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
