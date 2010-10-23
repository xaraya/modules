<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * get number of comments for all items or a list of items
 *
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['modid'] module id you want items from
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item IDs
 * @param $args['status'] optional status to count: ALL (minus root nodes), ACTIVE, INACTIVE
 * @param $args['numitems'] optional number of items to return
 * @param $args['startnum'] optional start at this number (1-based)
 * @returns array
 * @return $array[$itemid] = $numcomments;
 */
function comments_userapi_getitems($args)
{
  
    // Get arguments from argument array
    extract($args);

    if (!isset($modid) && isset($modname)) {
        $modid = xarMod::getRegID($modname);
    }

    /*if (!isset($itemtype)) {
        $itemtype = 0;
    }*/

    if (empty($status)) {
        $status = 'all';
    }
    $status = strtolower($status);

    // Security check
    if (!isset($mask)){
        $mask = 'ReadComments';
    }
    if (!xarSecurityCheck($mask)) return;

    // Database information
    /*$dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $commentstable = $xartable['comments'];*/

	$where = '';
	$join = ''; 

    switch ($status) {
        case 'active':
            $where .= "status eq ". _COM_STATUS_ON;
			$join = ' AND ';
            break;
        case 'inactive': 
            $where .= "status eq ". _COM_STATUS_OFF;
			$join = ' AND ';
            break;
        default:
        case 'all':
            $where .= "status ne ". _COM_STATUS_ROOT_NODE;
			$join = ' AND ';
    }

	if (isset($author)) {
		$where = $where . $join . 'author eq ' . $author;
		$join = ' AND ';
	}
	if (isset($itemtype) && !empty($itemtype)) { 
		$where = $where . $join . 'itemtype eq ' . $itemtype;
		$join = ' AND ';
	}
	if (isset($left_id) && isset($right_id)) {
		$where = $where . $join . 'left_id ge ' . $left_id;
		$join = ' AND ';
		$where = $where . $join . 'right_id le ' . $right_id;
	}
	if (isset($modid)) {
		$where = $where . $join . 'modid eq ' . $modid;
		$join = ' AND ';
	}
	if (isset($objectid)) {
		$where = $where . $join . 'objectid eq ' . $objectid;
	}

	if (isset($startnum)) {
		$filters['startnum'] = $startnum;
	}
	if (isset($numitems)) {
		$filters['numitems'] = $numitems;
	}

	if (!empty($where)) {
		$filters['where'] = $where;
	} else {
		$filters = array();
	}

	sys::import('modules.dynamicdata.class.objects.master');
	$list = DataObjectMaster::getObjectList(array(
							'name' => 'comments'
		));

	if (!is_object($list)) return;

	$items = $list->getItems($filters);  

	return $items;

    // Get items
    /*$bindvars = array();
    $query = "SELECT objectid, COUNT(*)
                FROM $commentstable
               WHERE modid = ?
                 AND itemtype = ?
                 AND $where_status ";
    $bindvars[] = (int) $modid; $bindvars[] = (int) $itemtype;
    if (isset($itemids) && count($itemids) > 0) {
        $bindmarkers = '?' . str_repeat(',?', count($itemids)-1);
        $bindvars = array_merge($bindvars, $itemids);
        $query .= " AND objectid IN ($bindmarkers)";
    }
    $query .= " GROUP BY objectid
                ORDER BY objectid";*/
//                ORDER BY (1 + objectid";
//
// CHECKME: dirty trick to try & force integer ordering (CAST and CONVERT are for MySQL 4.0.2 and higher
// <rabbitt> commented that line out because it won't work with PostgreSQL - not sure about others.

    /*if (!empty($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result = $dbconn->SelectLimit($query, $numitems, $startnum - 1,$bindvars);
    } else {
        $result = $dbconn->Execute($query,$bindvars);
    }*/

    /*if (!$result) return;

    $getitems = array();
    while (!$result->EOF) {
        list($id,$numcomments) = $result->fields;
        $getitems[$id] = $numcomments;
        $result->MoveNext();
    }
    $result->close();
    return $getitems;*/

}
?>
