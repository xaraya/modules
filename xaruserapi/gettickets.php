<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
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
    if( !isset($startnum) ){ $startnum = 1; }
    if( !isset($numitems) ){ $numitems = 20; }
    if( !isset($count) ){ $count = false; }
    if( !isset($selection) ){ $selection = ''; }
    if( !isset($sortorder) ){ $sortorder = ''; }

    // Database information
    $dbconn         = xarDBGetConn();
    $xartable       = xarDBGetTables();
    $helpdesktable  = $xartable['helpdesk_tickets'];

    xarSessionSetVar('ResultTitle', '');

    if( !xarVarFetch('override',  'str:1:', $override,  null,  XARVAR_NOT_REQUIRED) ){ return false; }

    /*
        Init query parts
    */
    if( $count == true )
    {
        $fields = array("COUNT(DISTINCT $helpdesktable.xar_id)");
    }
    else
    {
        $fields = array(
            "$helpdesktable.xar_id", "xar_date", "xar_subject", "xar_statusid",
            "xar_priorityid", "xar_updated", "xar_assignedto", "xar_openedby",
            "xar_closedby"
        );
    }
    $tables = array($helpdesktable);
    $from ='';
    $left_join = array();
    $where = array();
    $bindvars = array();

    //Joins on Catids
    if(!empty($catid))
    {
        $categoriesdef = xarModAPIFunc('categories', 'user', 'leftjoin',
                              array('modid'    => xarModGetIdFromName('helpdesk'),
                                    'itemtype' => TICKET_ITEMTYPE,
                                    'cids'     => array($catid),
                                    'andcids'  => 1));
    }

    /*
        If the security module is installed and ready to go, get ready to do a left join
    */
    if( xarModIsAvailable('security') )
    {
        $security_def = xarModAPIFunc('security', 'user', 'leftjoin',
            array(
                'modid'    => xarModGetIdFromName('helpdesk'),
                'itemtype' => TICKET_ITEMTYPE,
                'itemid'   => "$helpdesktable.xar_id",
                'user_field' => "$helpdesktable.xar_openedby",
                'level' => isset($level) ? $level : null,
                'limit_gids' => !empty($company) ? array($company) : null,
                // This exception insures that the tech assigned to the ticket can see it.
                // NOTE: At this point this is prolly not need but just want to make sure first.
                'exceptions' => array('xar_assignedto = ' . $dbconn->qstr(xarUserGetVar('uid')))
            )
        );
        if( count($security_def) > 0 )
        {
            if( isset($security_def['left_join']) )
                $left_join[] = " {$security_def['left_join']} ";
            if( isset($security_def['where']) )
                $where[] = " {$security_def['where']} ";
        }
    }

    // Get items Ticket Number/Date/Subject/Status/Last Update
    $sql  = 'SELECT ' . join(', ', $fields);
    $sql .= ' FROM ' .join(', ', $tables);

    if( !empty($catid) && count(array($catid)) > 0 )
    {
        // add this for SQL compliance when there are multiple JOINs
        // Add the LEFT JOIN ... ON ... parts from categories
        $left_join[] = ' LEFT JOIN ' . $categoriesdef['table']
            . ' ON ' . $categoriesdef['field'] . ' = ' . $helpdesktable . '.xar_id';

        if( !empty($categoriesdef['more']) )
        {
            $left_join[] = $categoriesdef['more'];
        }

        $where[] = $categoriesdef['where'];
    }

    /*
        Runs a couple conditions on the tickets

        TODO:
            Limit selections to
                MY  - Tickets user created, assigned to user
                ALL - All Tickets using only security filters
                MYASSIGNED - Tickets assigned to user
                UNASSIGNED - Tickets assigned to no one
    */
    switch($selection)
    {
        case 'ALL':
        case 'MYALL':
            break;

        case 'MYASSIGNEDALL':
            $where[] = "xar_assignedto = ?";
            $bindvars[] = (int)$userid;
            break;

        case 'UNASSIGNED':
            $where[] = "xar_assignedto < ?";
            $bindvars[] = 2;
            break;

        case 'MYPERSONAL':
        default:
            if( empty($userid) ){ $userid = xarUserGetVar('uid'); }
            $where[] = " ( xar_openedby = ? OR xar_assignedto = ? ) ";
            $bindvars[] = (int)$userid;
            $bindvars[] = (int)$userid;
            break;
    }

    // Status filter code
    if( !empty($statusfilter) )
    {
        $where[] = "xar_statusid = ? ";
        $bindvars[] = (int)$statusfilter;
    }

    /*
        keywords used in searching tickets
    */
    $whereor = array();
    if( !empty($keywords) && !empty($search_fields) )
    {
        $words = explode(" ", $keywords);
        foreach( $search_fields as $field )
        {
            if( $field != 'subject' ){ break; }
            foreach($words as $word)
            {
                $whereor[] = "(xar_$field LIKE " . $dbconn->qstr("%$word%") . ")";
            }
        }
    }

    if( isset($itemids) && is_array($itemids) && count($itemids) > 0 )
    {
        $where[] = " xar_id IN (?" . str_repeat(',?', count($itemids)-1) . ") ";
        foreach( $itemids as $itemid ){ $bindvars[] = (int)$itemid; }
    }

    /*
        Start putting the condition parts of the query together
    */
    if( count($left_join) > 0 ){ $sql .= join(' ', $left_join); }
    if( count($whereor) > 0 ){ $where[] = '(' . join(' OR ', $whereor) . ')'; }
    if( count($where) > 0 ){ $sql .= ' WHERE ' . join(' AND ', $where); }
    if( $count == true ){ $sql .= " GROUP BY $helpdesktable.xar_id"; }

    switch($sortorder)
    {
        case 'TICKET_ID':
            $sql .= " ORDER BY xar_id $order";
            break;
        case 'DATEUPDATED':
            $sql .= " ORDER BY xar_updated $order";
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

    if( $count == true )
    { $results = $dbconn->Execute($sql, $bindvars); }
    else
    { $results = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);  }
    if( !$results ){ return false; }

    if( $count == true ){ return $results->fields[0]; }

    /*
        Put items into result array
    */
    $tickets = array();
    while( (list($tid,        $ticketdate, $subject, $statusid, $priorityid, $lastupdate,
                $assignedto, $openedby,   $closedby) = $results->fields) != null )
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
