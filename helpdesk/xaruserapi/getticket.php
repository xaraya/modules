<?php
/**
    Get the desired ticket
    
    @author Brian McGilligan
    @param $args['tid'] - The tickets id number
    @return The Ticket in an array
*/
function helpdesk_userapi_getticket($args)
{
    //
    extract($args);
    if (!isset($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'ticket id', 'userapi', 'getticket', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];
    $db_column = &$xartable['helpdesk_tickets_column'];
    $sql = "SELECT  $db_column[ticket_priorityid],
                    $db_column[ticket_sourceid],
                    $db_column[ticket_openedby],
                    $db_column[ticket_subject],
                    xar_domain,
                    $db_column[ticket_date],
                    $db_column[ticket_statusid],
                    $db_column[ticket_assignedto],
                    $db_column[ticket_closedby],
                    $db_column[ticket_id],
                    $db_column[ticket_lastupdate],
                    xar_name,
                    xar_phone
            FROM    $db_table
            WHERE   $db_column[ticket_id] = $tid";

    $results = $dbconn->Execute($sql);
    if (!$results) { return false; }
    
    list($priorityid, $sourceid, $openedby,  $subject,    $domain,
         $ticketdate, $statusid, $assignedto, $closedby, $ticket_id, 
         $lastupdate, $name, $phone) = $results->fields;


    $cats = xarModAPIFunc('categories', 'user', 'getitemcats', 
                          array('modid'    => 910,
                                'itemtype' => 1,
                                'itemid'   => $ticket_id,
                               )
                         );
                                           
    $fieldresults = array(
        'tid'           => $ticket_id,
        'name'          => $name,
        'phone'         => $phone,
        'priority'      => xarModAPIFunc('helpdesk', 'user', 'get', array('object' => 'priority', 'itemid' => $priorityid)),
        'prioritycolor' => xarModAPIFunc('helpdesk', 'user', 'get', array('object' => 'priority', 'itemid' => $priorityid, 'field'=>'color')),
        'source'        => xarModAPIFunc('helpdesk', 'user', 'get', array('object' => 'source', 'itemid'   => $sourceid)),
        'openedby'      => $openedby,
        // NOTE : This statement must be done, if $openedby is null the the XarUserGetVar
        //        returns the name of the user running the app, which is very wrong to do
        'openedbyname'  => !empty($openedby) ? xarUserGetVar('name', $openedby) : '',
        'subject'       => $subject,
        'domain'        => $domain,
        'date'          => xarModAPIFunc('helpdesk', 'user', 'formatdate', array('date'     => $ticketdate)),
        'status'        => xarModAPIFunc('helpdesk', 'user', 'get', array('object' => 'status', 'itemid'   => $statusid, 'field'=> '')),
        'assignedto'    => $assignedto,
        'assignedtoname'=> !empty($assignedto) ? xarUserGetVar('name', $assignedto): '',
        'closedby'      => $closedby,
        'closedbyname'  => !empty($closedby) ? xarUserGetVar('name', $closedby) : '',
        'updated'       => xarModAPIFunc('helpdesk', 'user', 'formatdate', array('date' => $lastupdate)),
        'categories'    => $cats
        );
    return $fieldresults;
}
?>
