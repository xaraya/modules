<?php

/**
 * File: $Id$
 *
 * Count number of repositories
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * utility function to count the number of items held by this module
 *
 * @author the Bkview module development team
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function bkview_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $bkviewtable = $xartable['bkview'];

    $sql = "SELECT COUNT(1)
            FROM $bkviewtable";
    $result = $dbconn->Execute($sql);
    if(!$result) return;

    // Obtain the number of items
    list($numitems) = $result->fields;

    $result->Close();
    return $numitems;
}

?>