<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to do something
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_userapi_countposters($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    //$forumstable = $xartable['crispbb_forums'];
    //$topicstable = $xartable['crispbb_topics'];
    $poststable = $xartable['crispbb_posts'];
    //$hookstable = $xartable['crispbb_hooks'];

    $select = array();
    $where = array();
    $orderby = array();
    $bindvars = array();

    $from = $poststable;

    $rolesdef = xarModAPIFunc('roles', 'user', 'leftjoin');

    // Add the LEFT JOIN ... ON ... roles for the last post poster info
    $from .= ' LEFT JOIN ' . $rolesdef['table'];
    $from .= ' ON ' . $rolesdef['table'] . '.xar_uid' . ' = ' . $poststable . '.xar_powner';

    if (isset($ip) && is_string($ip)) {
        $where[] = $poststable . '.xar_phostname LIKE ?';
        $bindvars[] = $ip;
    }

    if (isset($powner) && is_numeric($powner)) {
        $where[] = $poststable . '.xar_powner = ?';
        $bindvars[] = $powner;
    }


    $query = 'SELECT COUNT(DISTINCT ' . $poststable . '.xar_powner)';
    $query .= ' FROM ' . $from;
    if (!empty($where)) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;

}
?>