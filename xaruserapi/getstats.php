<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
//=========================================================================
// Gets number of tickets in DB
//=========================================================================
function helpdesk_userapi_getstats()
{
    // Database information
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    $sql = "SELECT COUNT(xar_id)
            FROM $helpdesktable";
    $results = $dbconn->Execute($sql);
    if (!$results) return;

    list($ticketcount) = $results->fields;

    $results->Close();

    return $ticketcount;
}
?>
