<?php
function helpdesk_userapi_modifyticket($args)
{
extract($args);
    xarVarFetch('selection',  'str:1:',  $userid,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $ticket_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $history_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $ticketsubject,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $ticketprioritysel,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $statusid,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $tickettypesel,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $openedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $assignedto,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $ticketsource,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $closedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $issue,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $notes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $minutes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $software_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $swversion_id,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $xtra_fields,  null,  XARVAR_NOT_REQUIRED);
    list($userid,$ticket_id,$history_id,$ticketsubject,$ticketprioritysel,$statusid,$tickettypesel,
        $openedby,$assignedto,$ticketsource,$closedby,$issue,$notes,$hours,$minutes,$software_id,$swversion_id,$xtra_fields) =
        xarVarCleanFromInput('userid','ticket_id','history_id','ticketsubject','ticketprioritysel','statusid','tickettypesel',
        'openedby','assignedto','ticketsource','closedby','issue','notes','hours','minutes','software_id','swversion_id','xtra_fields');


// Generate SQL code for Ticket entry
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];
    $db_column = &$xartable['helpdesk_tickets_column'];
    $time = date("Y-m-d H:i:s");

    if (!$swversion_id){$swversion_id==0;}
    if (!$software_id){$software_id==0;}

    $sql = "UPDATE $db_table SET
        $db_column[ticket_typeid]		= '".xarVarPrepForStore($tickettypesel)."',
        $db_column[ticket_priorityid]	= '".xarVarPrepForStore($ticketprioritysel)."',
        $db_column[ticket_subject]		= '".xarVarPrepForStore($ticketsubject)."',
        $db_column[ticket_softwareid]	= '".xarVarPrepForStore($software_id)."',
        $db_column[ticket_swversionid]	= '".xarVarPrepForStore($swversion_id)."',
        $db_column[ticket_lastupdate]	= '".xarVarPrepForStore($time)."'";
        // The following If block is only executed if the user has EDIT access
        // Regular users may not change any of these fields
    if ($xtra_fields){
        $sql .=",		
        $db_column[ticket_sourceid]		= '".xarVarPrepForStore($ticketsource)."',
        $db_column[ticket_assignedto]	= '".xarVarPrepForStore($assignedto)."'";
        $closer=$closedby;
        if (($statusid =='3') && ($closedby < '1')){
            // User has changed status to closed but not specified closer
            // so, set current user as closer
            $closer = $userid;
        } 

        if ($closedby > '1') {
            // If a closer was specified but status wasn't changed to closed, then we need to set to close
            $closer		= $closedby;
            $statusid 	= 3;
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
    $sql .=" WHERE $db_column[ticket_id] 	= '$ticket_id'";
    // Uncomment the following line to debug
    //echo 'SQL: '.$sql;
    //return false;
    $dbconn->Execute($sql);
    // unrem next line for debugging.
    //xarSessionSetVar('errormsg',$sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        xarSessionSetVar('errormsg', _MODIFYTICKETFAILED." :: ".$sql);
        return false;
    }
    // Generate SQL code for History entry
    $db_table = $xartable['helpdesk_histories'];
    $db_column = &$xartable['helpdesk_histories_column'];

    $sql = "UPDATE $db_table SET
            $db_column[history]				= '".xarVarPrepForStore($issue)."',
            $db_column[history_updatedby]	= '".xarVarPrepForStore($userid)."'";
            if ($xtra_fields){
                // The following should only be updated for users with Edit Access to the module
                $sql .=", $db_column[history_notes]		= '".xarVarPrepForStore($notes)."',
                $db_column[history_hours]		= '".xarVarPrepForStore($hours)."',
                $db_column[history_minutes]		= '".xarVarPrepForStore($minutes)."'";
            }
            $sql .=" WHERE  $db_column[history_id] 	= '$history_id'";


    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) {
        xarSessionSetVar('errormsg', _MODIFYHISTFAILED." : ".$sql);
        return false;
    }		
    // unrem next line for debugging.
    //xarSessionSetVar('errormsg',$sql);
    xarResponseRedirect(xarModURL('helpdesk', 'user', 'viewticket',array('ticket_id'=>$ticket_id,'activity'=>'VIEWTICKET','userid'=>$userid)));
    return true;
}
?>
