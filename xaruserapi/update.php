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
/**
  Updates a Ticket
  @author Brian McGilligan
*/
function helpdesk_userapi_update($args)
{
    extract($args);

    // Generate SQL code for Ticket entry
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];

    $time = date("Y-m-d H:i:s");

    $sql = "UPDATE $db_table
            SET    xar_priorityid   = ?,
                   xar_subject      = ?,
                   xar_domain       = ?,
                   xar_openedby     = ?,
                   xar_updated      = ? ";
    $bindvars = array($priority, $subject, $domain, $openedby, $time);

    if(!empty($name))
    {
        $sql .= " , xar_name = ? ";
        $bindvars[] = $name;
    }
    if(!empty($phone))
    {
        $sql .= " , xar_phone = ?";
        $bindvars[] = $phone;
    }

    // The following If block is only executed if the user has EDIT access
    // Regular users may not change any of these fields
    if (!empty($assignedto) && !empty($source)){
        $sql .=", xar_sourceid      = ?
                , xar_assignedto    = ?";
        $bindvars[] = $source;
        $bindvars[] = $assignedto;
    }

    if ($statusid =='3' && empty($closedby))
    {
        // User has changed status to closed but not specified closer
        // so, set current user as closer
        $closer = $userid;
    }

    if (!empty($closedby))
    {
        // If a closer was specified but status wasn't changed to closed, then we need to set to close
        $closer        = $closedby;
        $statusid     = 3;
    }

    if ($statusid == '3')
    {
        $sql .=", xar_closedby = ?";
        $bindvars[] = $closer;
    }

    $sql .=", xar_statusid    = ?";
    $bindvars[] = $statusid;
    $sql .=" WHERE xar_id     = ?";
    $bindvars[] = $tid;

    $dbconn->Execute($sql, $bindvars);
    if( $dbconn->ErrorNo() != 0 ){ return false; }

    return true;
}
?>
