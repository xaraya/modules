<?php
/**
    Creates the Ticket
    @author Brian McGilligan
    @param - all the ticket info TODO List everything
    @return The new ticket id
*/
function helpdesk_userapi_create($args)
{
    extract($args);

    // Generate SQL code for Ticket entry
    $dbconn    =& xarDBGetConn();
    $xartable  =& xarDBGetTables();
    $db_table  = $xartable['helpdesk_tickets'];
    $db_column = &$xartable['helpdesk_tickets_column'];
    $time = date("Y-m-d H:i:s");

    if (empty($name)){ $name = xarUserGetVar('name', $whosubmit); }
    if (empty($email)){ $email = xarUserGetVar('email', $whosubmit); }
    if (empty($phone)){ $phone = ''; }

    //$id = $dbconn->GenID($db_column['ticket_id']);
    $sql = "INSERT INTO $db_table  (xar_domain,
                                    $db_column[ticket_statusid],
                                    $db_column[ticket_priorityid],
                                    $db_column[ticket_sourceid],
                                    $db_column[ticket_openedby],
                                    $db_column[ticket_subject],
                                    $db_column[ticket_date],
                                    $db_column[ticket_lastupdate],
                                    $db_column[ticket_assignedto],
                                    $db_column[ticket_closedby],
                                    xar_name,
                                    xar_phone,
                                    xar_email
                                   ) 
                           VALUES  ('".xarVarPrepForStore($domain)."',
                                    '".xarVarPrepForStore($status)."',
                                    '".xarVarPrepForStore($priority)."',
                                    '".xarVarPrepForStore($source)."',
                                    '".xarVarPrepForStore($whosubmit)."',
                                    '".xarVarPrepForStore($subject)."',
                                    '".xarVarPrepForStore($time)."',
                                    '".xarVarPrepForStore($time)."',
                                    '".xarVarPrepForStore($assignedto)."',
                                    '".xarVarPrepForStore($closedby)."',
                                    '".xarVarPrepForStore($name)."',
                                    '".xarVarPrepForStore($phone)."',
                                    '".xarVarPrepForStore($email)."'                                    
                                )";

    $result = $dbconn->Execute($sql);
    $id = $dbconn->Insert_ID();
    
    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) { return; }
    
    // To see their results, we redirect them to the Manage category page:
    return $id;
}
?>
