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
    
    //get next id for Ticket
    $newticket_id = helpdesk_new_id(array('table'=>'tickets','field'=>'ticket_id'));
    
    // Generate SQL code for Ticket entry
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];
    $db_column = &$xartable['helpdesk_tickets_column'];
    $time = date("Y-m-d H:i:s");

    if (empty($swv_id)){ $swv_id = 0; }
    if (empty($sw_id)){ $sw_id = 0; }
    if (empty($name)){ $name = xarUserGetVar('name', $whosubmit); }
    if (empty($email)){ $email = xarUserGetVar('email', $whosubmit); }
    if (empty($phone)){ $phone = ''; }

    $sql = "INSERT INTO $db_table  ($db_column[ticket_id],
                                    xar_domain,
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
                           VALUES  (".$newticket_id.",
                                    '".xarVarPrepForStore($domain)."',
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

    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so set an
    // appropriate error message and return
    if ($dbconn->ErrorNo() != 0) { return; }
    
    // To see their results, we redirect them to the Manage category page:
    return $newticket_id;
}
?>
