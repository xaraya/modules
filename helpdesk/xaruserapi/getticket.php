<?php
/**
    Get the desired ticket
    
    @author Brian McGilligan
    @param $args['tid'] - The tickets id number
    @return The Ticket in an array
*/
function helpdesk_userapi_getticket($args)
{
    extract($args);
    
    if (!isset($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'ticket id', 'userapi', 'getticket', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $db_table = $xartable['helpdesk_tickets'];

    $sql = "SELECT  xar_priorityid,
                    xar_sourceid,
                    xar_openedby,
                    xar_subject,
                    xar_domain,
                    xar_date,
                    xar_statusid,
                    xar_assignedto,
                    xar_closedby,
                    xar_id,
                    xar_updated,
                    xar_name,
                    xar_phone
            FROM    $db_table
            WHERE   xar_id = ?";

    $results = $dbconn->Execute($sql, array($tid));
    if (!$results) { return false; }
    
    list($priorityid, $sourceid, $openedby,   $subject,  $domain,
         $ticketdate, $statusid, $assignedto, $closedby, $ticket_id, 
         $lastupdate, $name,     $phone) = $results->fields;


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
