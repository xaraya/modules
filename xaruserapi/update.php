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
    xarVarFetch('openedby',   'str:1:',  $openedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('assignedto', 'str:1:',  $assignedto,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('source',     'str:1:',  $source,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('closedby',   'str:1:',  $closedby,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('comment',    'str:1:',  $issue,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('notes',      'str:1:',  $notes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('hours',      'str:1:',  $minutes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('minutes',    'str:1:',  $minutes,  null,  XARVAR_NOT_REQUIRED);
    xarVarFetch('selection',  'str:1:',  $xtra_fields,  null,  XARVAR_NOT_REQUIRED);

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
    
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) { return false; }
    
    return true;
}
?>
