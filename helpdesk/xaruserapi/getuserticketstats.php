<?php
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
    $helpdeskcolumn = &$xartable['helpdesk_tickets_column'];
    
    // First Get Closed Ticket Count
    $sql = "SELECT count($helpdeskcolumn[ticket_id])
            FROM $helpdesktable
            WHERE ($helpdeskcolumn[ticket_statusid] = 3 
            AND $helpdeskcolumn[ticket_openedby] = $userid)";
    $results = $dbconn->Execute($sql);
    if (!$results) {
        $msg = xarML('DB query failed for #(2) function #(3)() in module #(4)',
                     'null', 'userapi', 'getuserticketstats', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        $results->Close();
        return false;
    }
    list($closedcount) = $results->fields;
    $results->Close();
    
    // Now Get total count
    $sql = "SELECT count($helpdeskcolumn[ticket_id])
            FROM  $helpdesktable
            WHERE $helpdeskcolumn[ticket_openedby] = $userid";
    $results = $dbconn->Execute($sql);
    if (!$results) {
        $msg = xarML('DB query failed for #(2) function #(3)() in module #(4)',
                     'null', 'userapi', 'getuserticketstats', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        $results->Close();
        return false;
    }
    list($totalcount) = $results->fields;
    $results->Close();
    
    // Now, let's get the assigned ticket information:
    $sql = "SELECT count($helpdeskcolumn[ticket_id])
            FROM  $helpdesktable
            WHERE ($helpdeskcolumn[ticket_statusid] <> 3 
            AND $helpdeskcolumn[ticket_assignedto] = $userid)";
    $results = $dbconn->Execute($sql);
    if (!$results) {
        $msg = xarML('DB query failed for #(2) function #(3)() in module #(4)',
                     'null', 'userapi', 'getuserticketstats', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        $results->Close();
        return false;
    }
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
