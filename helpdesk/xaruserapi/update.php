<?php
/**
  Updates a Ticket
  @author Brian McGilligan
*/
function helpdesk_userapi_update($args)
{
    extract($args);
    xarVarFetch('userid',     'str:1:',  $userid,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('tid',        'str:1:',  $tid,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('name',       'str:1:',  $name,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('phone',      'str:1:',  $phone,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('subject',    'str:1:',  $subject,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('domain',     'str:1:',  $domain,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('priority',   'str:1:',  $priority,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('status',     'str:1:',  $statusid,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('typeid',     'str:1:',  $type,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('openedby',   'str:1:',  $openedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('assignedto', 'str:1:',  $assignedto,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('source',     'str:1:',  $source,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('closedby',   'str:1:',  $closedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('comment',    'str:1:',  $issue,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',      'str:1:',  $notes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('hours',      'str:1:',  $minutes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('minutes',    'str:1:',  $minutes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('sw_id',      'str:1:',  $software_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('swv_id',     'str:1:',  $swversion_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $xtra_fields,  null,  XARVAR_NOT_REQUIRED);

    // Generate SQL code for Ticket entry
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];
    $db_column = &$xartable['helpdesk_tickets_column'];
    $time = date("Y-m-d H:i:s");

    if (!$swversion_id){$swversion_id=0;}
    if (!$software_id){$software_id=0;}

    $sql = "UPDATE $db_table SET
        $db_column[ticket_typeid]		= '".xarVarPrepForStore($type)."',
        $db_column[ticket_priorityid]	= '".xarVarPrepForStore($priority)."',
        $db_column[ticket_subject]		= '".xarVarPrepForStore($subject)."',
        $db_column[ticket_domain]		= '".xarVarPrepForStore($domain)."',
        $db_column[ticket_openedby]     = '".xarVarPrepForStore($openedby)."',
        $db_column[ticket_softwareid]	= '".xarVarPrepForStore($software_id)."',
        $db_column[ticket_swversionid]	= '".xarVarPrepForStore($swversion_id)."',
        $db_column[ticket_lastupdate]	= '".xarVarPrepForStore($time)."'";
    if(!empty($name)) { $sql .= " , xar_name = '" . xarVarPrepForStore($name) . "'"; }
    if(!empty($phone)) { $sql .= " , xar_phone = '" . xarVarPrepForStore($phone) . "'"; }
        // The following If block is only executed if the user has EDIT access
        // Regular users may not change any of these fields
        // NOTE: I don't like this I am going to change this, with the way Xaraya
        //       is set up I don't think half of this is needed
    if (!empty($assignedto) && !empty($source)){
        $sql .=",		
        $db_column[ticket_sourceid]		= '".xarVarPrepForStore($source)."',
        $db_column[ticket_assignedto]	= '".xarVarPrepForStore($assignedto)."'";
        $closer = $closedby;
        if (($statusid =='3') && ($closedby < '1')){
            // User has changed status to closed but not specified closer
            // so, set current user as closer
            $closer = $userid;
        } 

        if ($closedby > '1') {
            // If a closer was specified but status wasn't changed to closed, then we need to set to close
            $closer		= $closedby;
            //$statusid 	= 3;
        }
        if ($statusid == '3'){
            $sql .=", $db_column[ticket_closedby]		= '".$closer."'";
        }
    } else {
        // User does not have Edit access, if he/she changes status to closed,
        // then set closed by to userid
        if ($statusid == 3){
            // Then the submitting user has closed the ticket
            $sql .=", $db_column[ticket_closedby]		= '".xarVarPrepForStore($userid)."'";
        }
    }
    $sql .=", $db_column[ticket_statusid]	= '".xarVarPrepForStore($statusid)."'";
    $sql .=" WHERE $db_column[ticket_id] 	= '$tid'";
    // Uncomment the following line to debug
    //return false;
    $dbconn->Execute($sql);
    
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) { return false; }
    
    return true;
}
?>
