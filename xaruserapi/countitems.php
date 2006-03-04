<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function censor_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $censortable = $xartable['censor'];
    $query = "SELECT COUNT(1)
              FROM $censortable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>