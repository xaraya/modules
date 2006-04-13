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

    /*
        Start building parts of the query
    */
    $fields = array(
        'xar_priorityid',
        'xar_sourceid',
        'xar_openedby',
        'xar_subject',
        'xar_domain',
        'xar_date',
        'xar_statusid',
        'xar_assignedto',
        'xar_closedby',
        'xar_id',
        'xar_updated',
        'xar_name',
        'xar_phone'
    );
    $tables = array($db_table);
    $left_join = array();
    $where = array('xar_id = ?');
    $bindvars = array($tid);

    /*
        NEED TO JOIN WITH SECURITY MODULE
    */
    if( xarModIsAvailable('security') )
    {
        $security_def = xarModAPIFunc('security', 'user', 'leftjoin',
            array(
                'modid'    => xarModGetIdFromName('helpdesk'),
                'itemtype' => 1,
                'itemid'   => "xar_id",
                'user_field' => "$db_table.xar_openedby",
                'level' => isset($level) ? $level : null,
                // This exception insures that the tech assigned to the ticket can see it.
                'exception' => 'xar_assignedto = ' . $dbconn->qstr(xarUserGetVar('uid'))
            )
        );
        if( count($security_def) > 0 )
        {
            $left_join[] = " {$security_def['left_join']} ";
            $where[] = "( {$security_def['where']} )";
        }
    }

    /*
        Build the query
    */
    $sql  = 'SELECT ' . join(', ', $fields);
    $sql .= ' FROM '  . join(', ', $tables);
    if( count($left_join) > 0 ){ $sql .= join(' ', $left_join); }
    $sql .= ' WHERE ' . join(' AND ', $where);

    $results = $dbconn->Execute($sql, array($tid));
    if( !$results ){ return false; }

    if( $results->EOF ){ return false; }

    list($priorityid, $sourceid, $openedby,   $subject,  $domain,
         $ticketdate, $statusid, $assignedto, $closedby, $ticket_id,
         $lastupdate, $name,     $phone) = $results->fields;

    $cats = xarModAPIFunc('categories', 'user', 'getitemcats',
        array(
            'modid'    => 910,
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
