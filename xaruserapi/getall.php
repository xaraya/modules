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
    Get the all the security


    NOT IMPLEMENTED YET!

*/
function security_userapi_getall($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['security_roles'];
    $return = array();

    if( $mode == "modules" )
    {
        $bindvars = array();
        $where = array();
        $query = "SELECT modid "
            . "FROM $table "
            . "GROUP BY modid "
        ;
        if( count($where) > 0 )
        {
            $query .= ' WHERE ' . join(' AND ', $where);
        }
        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ){ return false; }
        if( $result->EOF ){ return array(); }


        while( (list($modid) = $result->fields) != null )
        {
            $return[$modid] = array(
                'num_items' => ''
            );

            $result->MoveNext();
        }
    }
    elseif( $mode == 'itemtypes' )
    {
        $bindvars = array();
        $where = array();
        $query = "SELECT itemtype "
            . "FROM $table "
        ;
        $where[] = " modid = ? ";
        $bindvars[] = $modid;

        if( count($where) > 0 )
        {
            $query .= ' WHERE ' . join(' AND ', $where);
        }

        $query .= "GROUP BY modid, itemtype ";

        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ){ return false; }
        if( $result->EOF ){ return array(); }

        $module = xarModGetInfo($modid);
        $itemtypes = xarModAPIFunc($module['name'], 'user', 'getitemtypes', array(), false);
        while( (list($itemtype) = $result->fields) != null )
        {
            $return[$itemtype] = $itemtypes[$itemtype];
            $return[$itemtype]['num_items'] = '';

            $result->MoveNext();
        }
    }

    elseif( $mode == 'items' )
    {
        $bindvars = array();
        $where = array();
        $query = "SELECT itemid, COUNT(uid)"
            . "FROM $table "
        ;
        $where[] = " modid = ? ";
        $bindvars[] = $modid;
        $where[] = " itemtype = ? ";
        $bindvars[] = $itemtype;

        if( count($where) > 0 )
        {
            $query .= ' WHERE ' . join(' AND ', $where);
        }

        $query .= "GROUP BY modid, itemtype, itemid ";

        $result = $dbconn->Execute($query, $bindvars);
        if( !$result ){ return false; }
        if( $result->EOF ){ return array(); }

        $itemids = array();
        while( (list($itemid) = $result->fields) != null )
        {
            $itemids[] = $itemid;
            $result->MoveNext();
        }
        $module = xarModGetInfo($modid);
        $items = xarModAPIFunc($module['name'], 'user', 'getitemlinks',
            array(
                'itemtype' => $itemtype,
                'itemids' => $itemids
            )
        );

        $result->MoveFirst();
        while( (list($itemid, $count) = $result->fields) != null )
        {
            $return[$itemid] = isset($items[$itemid]) ? $items[$itemid] : xarML('Unknown');
            $return[$itemid]['num_items'] = $count;

            $result->MoveNext();
        }
    }


    return $return;
}
?>