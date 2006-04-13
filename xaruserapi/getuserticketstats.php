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
// Get Help Desk Statistics:
//=========================================================================
function helpdesk_userapi_getuserticketstats($args)
{
    extract($args);
    // Database information
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    // First Get Closed Ticket Count
    $sql = "SELECT count(xar_id)
            FROM $helpdesktable
            WHERE (xar_statusid = ? AND xar_openedby = ?)";
    $bindvars = array(3, $userid);
    $results = $dbconn->Execute($sql, $bindvars);
    if ( !$results ){ return false; }
    list($closedcount) = $results->fields;
    $results->Close();

    // Now Get total count
    $sql = "SELECT count(xar_id)
            FROM  $helpdesktable
            WHERE xar_openedby = ?";
    $bindvars = array($userid);
    $results = $dbconn->Execute($sql, $bindvars);
    if ( !$results ){ return false; }
    list($totalcount) = $results->fields;
    $results->Close();

    // Now, let's get the assigned ticket information:
    $sql = "SELECT count(xar_id)
            FROM  $helpdesktable
            WHERE (xar_statusid <> ? AND xar_assignedto = ?)";
    $bindvars = array(3, $userid);
    $results = $dbconn->Execute($sql, $bindvars);
    if ( !$results ){ return false; }
    list($assignedopen) = $results->fields;
    $results->Close();

    $returndata = array(
        'total'        => $totalcount,
        'open'         => $totalcount-$closedcount,
        'closed'       => $closedcount,
        'assignedopen' => $assignedopen
    );

    return $returndata;
}
?>
