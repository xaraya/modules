<?php
/**
    Get the Tickets in the database
    
    @author Brian McGilligan
    @param
    @return The tickets in the database
*/
function helpdesk_userapi_gettickets($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if(!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = 20;
    }

    // Database information
    $dbconn         =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];
    
    xarSessionSetVar('ResultTitle', '');
    
    xarVarFetch('override',  'str:1:', $override,  null,  XARVAR_NOT_REQUIRED);

    //Joins on Catids
    if(!empty($catid))
    {
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin', 
                              array('modid'    => 910,
                                    'itemtype' => 1,
                                    'cids'     => array($catid),
                                    'andcids'  => 1));
    }
    
    // Get items Ticket Number/Date/Subject/Status/Last Update
    $sql = "SELECT DISTINCT  $helpdesktable.xar_id,
                             xar_date,
                             xar_subject,
                             xar_statusid,
                             xar_priorityid,
                             xar_updated,
                             xar_assignedto,
                             xar_openedby,
                             xar_closedby
                FROM $helpdesktable ";
                
    $from ='';
    $where = array();
    $bindvars = array();
    if (!empty($catid) && count(array($catid)) > 0) 
    {
        // add this for SQL compliance when there are multiple JOINs
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $helpdesktable . '.xar_id';
        
        if (!empty($categoriesdef['more'])) 
        {
            $from .= $categoriesdef['more'];
        }
        
        $where[] = $categoriesdef['where'];
        $sql .= $from;
    }
    
    switch($selection) {
        case 'UNASSIGNED':            
            xarSessionSetVar('ResultTitle', xarML('Unassigned Tickets'));
            $where[] = "xar_assignedto < ?";
            $bindvars[] = 2;
            break;
        case 'MYALL':
            xarSessionSetVar('ResultTitle', xarML('All Your Tickets'));
            $where[] = "xar_openedby = ?";
            $bindvars[] = $userid;
            break;
        case 'MYOPEN':
            xarSessionSetVar('ResultTitle', xarML('All Your Open Tickets'));
            $where[] = "xar_openedby = ?";
            $where[] = "xar_statusid != ? ";
            $bindvars[] = $userid;
            $bindvars[] = 3;
            break;
        case 'MYCLOSED':
            xarSessionSetVar('ResultTitle', xarML('All Your Closed Tickets'));
            $where[] = "xar_openedby = ?";
            $where[] = "xar_statusid = ?";
            $bindvars[] = $userid;
            $bindvars[] = 3;
            break;
        case 'ALL':
            xarSessionSetVar('ResultTitle', xarML('All Tickets in Database'));
            break;
        case 'OPEN':
            xarSessionSetVar('ResultTitle', xarML('All Open Tickets'));
            $where[] = "xar_statusid != ?";
            $bindvars[] = 3;
            break;
        case 'CLOSED':
            xarSessionSetVar('ResultTitle', xarML('All Closed Tickets'));
            $where[] = "xar_statusid = ?";
            $bindvars[] = 3;
            break;
        case 'MYASSIGNEDALL':
            xarSessionSetVar('ResultTitle', xarML('All Your Assigned Tickets'));
            $where[] = "xar_assignedto = ?";
            $bindvars[] = $userid;
            break;
        case 'MYASSIGNEDOPEN':
            xarSessionSetVar('ResultTitle', xarML('All Your Open Assigned Tickets'));
            $where[] = "xar_assignedto = ?";
            $where[] = "xar_statusid != ?";
            $bindvars[] = $userid;
            $bindvars[] = 3;
            break;
        case 'MYASSIGNEDCLOSED':
            xarSessionSetVar('ResultTitle', xarML('All Your Closed Assigned Tickets'));
            $where[] = "xar_assignedto = ?";
            $where[] = "xar_statusid = ?";
            $bindvars[] = $userid;
            $bindvars[] = 3;
            break;
    }
    
    // Status filter code
    if(!empty($statusfilter)){
        $where[] = "xar_statusid = ? ";
        $bindvars[] = $statusfilter;
    }
    
    $whereor = array();
    if(!empty($keywords) && !empty($subject))
    {
        $words = explode(" ", $keywords);
        foreach($words as $word)
        {
            $whereor[] = "(xar_subject LIKE '%?%')";
            $bindvars[] = $word;
        }
    }
    
    if(!empty($userid) && !xarSecurityCheck('edithelpdesk', 0))
    {
        $where[] = "xar_openedby = ?";
        $bindvars[] = $userid;
    }
    
    if(count($where) > 0)
    {
        $sql .= ' WHERE ' . join(' AND ', $where);
    }
    if(count($whereor) > 0)
    {
        if(count($where) == 0)
        {
            $sql .= ' WHERE ' . join(' OR ', $whereor);        
        }
        else
        {
            $sql .= ' OR ' . join(' OR ', $whereor);
        }
    }
    
    switch($sortorder) 
    {
    case 'TICKET_ID':
        $sql .= " ORDER BY xar_id $order";
        break;
    case 'DATEUPDATED':
        $sql .= " ORDER BY xar_lastupdate $order";
        break;
    case 'DATE':
        $sql .= " ORDER BY xar_date $order";
        break;
    case 'SUBJECT':
        $sql .= " ORDER BY xar_subject $order";
        break;
    case 'PRIORITY':
        $sql .= " ORDER BY xar_priorityid $order";
        break;
    case 'STATUS':
        $sql .= " ORDER BY xar_statusid $order";
        break;
    case 'OPENEDBY':
        $sql .= " ORDER BY xar_openedby $order";
        break;
    case 'ASSIGNEDTO':
        $sql .= " ORDER BY xar_assignedto $order";
        break;
    case 'CLOSEDBY':
        $sql .= " ORDER BY xar_closedby $order";
        break;
    }
    
    $pagerows = xarModGetVar('helpdesk', 'Default rows per page');
    --$startnum;
    $sql .= " LIMIT  $startnum , $numitems";

    $results = $dbconn->Execute($sql, $bindvars); //$numitems, $startnum-1);
    // Check for an error
    if (!$results) { return false; }

    // Put items into result array
    $tickets = array();
    while( list($tid,        $ticketdate, $subject, $statusid, $priorityid, $lastupdate,
                $assignedto, $openedby,   $closedby) = $results->fields ) 
    {
        $tickets[$tid] = array(
            'ticket_id'     => $tid, 
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

    return $tickets;
}

?>
