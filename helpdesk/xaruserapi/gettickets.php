<?php
/**
    Get the Tickets in the database
    
    TODO REDO THIS FUNCTION IT IS GETTING REALLY DIRTY
    
    @author Brian McGilligan
    @param
    @return The tickets in the database
*/
function helpdesk_userapi_gettickets($args)
{
    extract($args);
    // Database information
    $dbconn         =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];
    $helpdeskcolumn = &$xartable['helpdesk_tickets_column'];
    
    xarSessionSetVar('ResultTitle', '');
    
    xarVarFetch('override',  'str:1:', $override,  null,  XARVAR_NOT_REQUIRED);

    //Joins on Catids
    if(!empty($catid))
    {
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin', 
                              array('modid'    => 910,
                                    'itemtype' => 1,
                                    'cids'     => array($catid),
                                    'andcids'  => 0));
    }
    
    // Get items Ticket Number/Date/Subject/Status/Last Update
    $sql = "SELECT DISTINCT  $helpdeskcolumn[ticket_id],
            $helpdeskcolumn[ticket_date],
            $helpdeskcolumn[ticket_subject],
            $helpdeskcolumn[ticket_statusid],
            $helpdeskcolumn[ticket_priorityid],
            $helpdeskcolumn[ticket_lastupdate],
            $helpdeskcolumn[ticket_assignedto],
            $helpdeskcolumn[ticket_openedby],
            $helpdeskcolumn[ticket_closedby]
        FROM    $helpdesktable ";
    $from ='';
    if (!empty($catid) && count(array($catid)) > 0) {
        // add this for SQL compliance when there are multiple JOINs
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . 'xar_id';
        /*
        if (!empty($categoriesdef['more'])) {
            $from = '(' . $from . ')';
            $from .= $categoriesdef['more'];
        }*/
        $from .= " WHERE " . $categoriesdef['where'];
        $sql .= $from;
    }
    else
    {
        $sql .= "WHERE $helpdeskcolumn[ticket_id] = $helpdeskcolumn[ticket_id]";
    }
    
    switch($selection) {
        case 'UNASSIGNED':            
            xarSessionSetVar('ResultTitle', xarML('Unassigned Tickets'));
            $sql .= " AND $helpdeskcolumn[ticket_assignedto] < 2";
            break;
        case 'MYALL':
            xarSessionSetVar('ResultTitle', xarML('All Your Tickets'));
            $sql .= " AND $helpdeskcolumn[ticket_openedby]=$userid";
            break;
        case 'MYOPEN':
            xarSessionSetVar('ResultTitle', xarML('All Your Open Tickets'));
            $sql .= " AND ($helpdeskcolumn[ticket_openedby]=$userid";
            $sql .= " AND $helpdeskcolumn[ticket_statusid]!=3)";
            break;
        case 'MYCLOSED':
            xarSessionSetVar('ResultTitle', xarML('All Your Closed Tickets'));
            $sql .= " AND ($helpdeskcolumn[ticket_openedby]=$userid";
            $sql .= " AND $helpdeskcolumn[ticket_statusid]=3)";
            break;
        case 'ALL':
            xarSessionSetVar('ResultTitle', xarML('All Tickets in Database'));
            break;
        case 'OPEN':
            xarSessionSetVar('ResultTitle', xarML('All Open Tickets'));
            $sql .= " AND $helpdeskcolumn[ticket_statusid]!=3";
            break;
        case 'CLOSED':
            xarSessionSetVar('ResultTitle', xarML('All Closed Tickets'));
            $sql .= " AND $helpdeskcolumn[ticket_statusid]=3";
            break;
        case 'MYASSIGNEDALL':
            xarSessionSetVar('ResultTitle', xarML('All Your Assigned Tickets'));
            $sql .= " AND $helpdeskcolumn[ticket_assignedto]=$userid";
            break;
        case 'MYASSIGNEDOPEN':
            xarSessionSetVar('ResultTitle', xarML('All Your Open Assigned Tickets'));
            $sql .= " AND ($helpdeskcolumn[ticket_assignedto]=$userid";
            $sql .= " AND $helpdeskcolumn[ticket_statusid]!=3)";
            break;
        case 'MYASSIGNEDCLOSED':
            xarSessionSetVar('ResultTitle', xarML('All Your Closed Assigned Tickets'));
            $sql .= " AND ($helpdeskcolumn[ticket_assignedto]=$userid";
            $sql .= " AND $helpdeskcolumn[ticket_statusid]=3)";
            break;
    }
    
    // Status filter code
    if(!empty($statusfilter)){
        $sql .= " AND $helpdeskcolumn[ticket_statusid] = $statusfilter ";    
    }
    
    // I don't like this code
    if(!empty($keywords) && !empty($subject)){
        $words = explode(" ", $keywords);
        foreach($words as $word)
        {
            $sql .= " OR ($helpdeskcolumn[ticket_subject] LIKE '%" . $word . "%') ";
        }
    }
    
    if(!empty($userid) && !xarSecurityCheck('edithelpdesk', 0))
    {
        $sql .= " AND  $helpdeskcolumn[ticket_openedby] = $userid ";
    }
    
    switch($sortorder) {
    case 'TICKET_ID':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_id] $order";
        break;
    case 'DATEUPDATED':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_lastupdate] $order";
        break;
    case 'DATE':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_date] $order";
        break;
    case 'SUBJECT':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_subject] $order";
        break;
    case 'PRIORITY':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_priorityid] $order";
        break;
    case 'STATUS':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_statusid] $order";
        break;
    case 'OPENEDBY':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_openedby] $order";
        break;
    case 'ASSIGNEDTO':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_assignedto] $order";
        break;
    case 'CLOSEDBY':
        $sql .= " ORDER BY $helpdeskcolumn[ticket_closedby] $order";
        break;
    }
    
    $pagerows = xarModGetVar('helpdesk', 'Default rows per page');
    
    // Note to self this is getting more complex than I'd like
    // Lets see if there is a cleaner way
    if(empty($startnum)){$startnum='0';}
    elseif($startnum > 0){ $startnum--; }
    if(empty($pagerows)){$pagerows='10';}
    $sql .= " LIMIT $startnum, $pagerows";
    
    $results = $dbconn->Execute($sql);
    if (!$results) { return false; }
    
    $fieldresults = array();
    while(list($ticket_id,  $ticketdate, $subject, $statusid, $priorityid, $lastupdate,
          $assignedto, $openedby,   $closedby) = $results->fields) {
    $fieldresults[] = array(
        'ticket_id'     => $ticket_id, 
        'ticketdate'    => xarModAPIFunc('helpdesk', 'user', 'formatdate', array('date' => $ticketdate)),
        'subject'       => $subject,
        'status'        => xarModAPIFunc('helpdesk', 'user', 'get', array('object' => 'status', 'itemid'   => $statusid, 'field'=> '')),
        'priority'      => xarModAPIFunc('helpdesk', 'user', 'get', array('object' => 'priority', 'itemid' => $priorityid)),
        'lastupdate'    => xarModAPIFunc('helpdesk', 'user', 'formatdate', array('date' => $lastupdate)),
        'assignedto'    => $assignedto,
        'openedby'      => $openedby,
        'closedby'      => $closedby
        );
    $results->MoveNext();
    }
    
    $results->close();

    return $fieldresults;
}
?>
