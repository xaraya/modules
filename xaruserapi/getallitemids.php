<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
*/
function security_userapi_getallitemids($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security_roles'];
    $return = array();
    $bindvars = array();
    $where = array();

    $query = "SELECT itemid, COUNT(uid)"
        . "FROM $table "
    ;
    $where[] = " modid = ? ";
    $bindvars[] = $modid;
    $where[] = " itemtype = ? ";
    $bindvars[] = $itemtype;
    $where[] = " itemid > ? ";
    $bindvars[] = 0;

    if( count($where) > 0 )
    {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

    $query .= "GROUP BY modid, itemtype, itemid ";

    $result = $dbconn->Execute($query, $bindvars);
    if( !$result ){ return false; }
    if( $result->EOF ){ return array(); }

//    $itemids = array();
//    while( (list($itemid) = $result->fields) != null )
//    {
//        $itemids[] = $itemid;
//        $result->MoveNext();
//    }
//    $module = xarModGetInfo($modid);
//    $items = xarModAPIFunc($module['name'], 'user', 'getitemlinks',
//        array(
//            'itemtype' => $itemtype,
//            'itemids' => $itemids
//        )
//        , false
//    );
//
//    $result->MoveFirst();
    while( (list($itemid, $count) = $result->fields) != null )
    {
        $return[$itemid] = $itemid; //isset($items[$itemid]) ? $items[$itemid] : xarML('Unknown');
        //$return[$itemid]['num_items'] = $count;

        $result->MoveNext();
    }


    return $return;
}
?>