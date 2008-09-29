<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function smilies_userapi_countitems($args)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Security Check
    if(!xarSecurityCheck('OverviewSmilies')) return;
    extract($args);
    $smiliestable = $xartable['smilies'];

    $query = "SELECT COUNT(1)
            FROM $smiliestable";
    // Bug 5116: Hide duplicates, applies to counts also for pager to work
    if (isset($groupby)) {
      switch ($groupby) {
        case 'emotion':
          $query .= " GROUP BY xar_emotion";
        break;
      }
    }
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>