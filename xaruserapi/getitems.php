<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 * @author Marc Lutolf <mfl@netspan.ch>
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

    if (!isset($moduleid) && isset($modname)) {
        $moduleid = xarMod::getRegID($modname);
    }

    /*if (!isset($itemtype)) {
        $itemtype = 0;
    }*/

    if (empty($status)) {
        $status = _COM_STATUS_ROOT_NODE;
    }

    switch ($status) {
        case 'active':
            $where_status = "status = ". _COM_STATUS_ON;
            $join = ' AND ';
            break;
        case 'inactive':
            $where_status = "status = ". _COM_STATUS_OFF;
            $join = ' AND ';
            break;
        default:
        case 'all':
            $where_status = "status != ". _COM_STATUS_ROOT_NODE;
            $join = ' AND ';
    }

    // Security check
    if (!isset($mask)) {
        $mask = 'ReadComments';
    }
    if (!xarSecurity::check($mask)) {
        return;
    }

    // Database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $commentstable = $xartable['comments'];

    $where = '';
    $join = '';

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
    if (isset($moduleid)) {
        $where = $where . $join . 'moduleid eq ' . $moduleid;
        $join = ' AND ';
    }
    if (isset($itemid)) {
        $where = $where . $join . 'itemid eq ' . $itemid;
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
        $filters = [];
    }

    sys::import('modules.dynamicdata.class.objects.master');
//    $list = DataObjectMaster::getObjectList(array(
//                            'name' => 'comments_comments'
//        ));

//    if (!is_object($list)) return;

//    $items = $list->getItems($filters);

//    return $items;

    sys::import('xaraya.structures.query');
    $tables =& xarDB::getTables();
    $q = new Query('SELECT', $tables['comments']);
    $q->eq('module_id', $moduleid);
    $q->eq('itemtype', $itemtype);
    $q->eq('status', (int)$status);
    if (isset($itemids) && count($itemids) > 0) {
        $q->in('itemid', $itemids);
    } elseif (isset($itemid)) {
        $q->in('itemid', $itemid);
    }
//    $q->setgroup('itemid');
    $q->setorder('itemid', 'ASC');
    if (!empty($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $q->setstartat($startnum);
        $q->setrowstodo($numitems);
    }
    $q->run();
    $items = $q->output();

    // Get items
    $bindvars = [];
    $query = "SELECT id,parent_id,parent_url,title,text,left_id,right_id,module_id,itemtype,itemid,date,author,anonpost, COUNT(*)
                FROM $commentstable
               WHERE module_id = ?
                 AND itemtype = ?
                 AND $where_status ";
    $bindvars[] = (int) $moduleid;
    $bindvars[] = (int) $itemtype;
    if (isset($itemids) && count($itemids) > 0) {
        $bindmarkers = '?' . str_repeat(',?', count($itemids)-1);
        $bindvars = array_merge($bindvars, $itemids);
        $query .= " AND itemid IN ($bindmarkers)";
    }
    $query .= " GROUP BY id, itemid
                ORDER BY itemid";
//                ORDER BY (1 + objectid";
//
    // CHECKME: dirty trick to try & force integer ordering (CAST and CONVERT are for MySQL 4.0.2 and higher
    // <rabbitt> commented that line out because it won't work with PostgreSQL - not sure about others.

    if (!empty($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result = $dbconn->SelectLimit($query, $numitems, $startnum - 1, $bindvars);
    } else {
        $result = $dbconn->Execute($query, $bindvars);
    }

    if (!$result) {
        return;
    }

    /*    $items = array();
        while (!$result->EOF) {
            list($id,$parent_id,$parent_url,$title,$text,$left_id,$right_id,$moduleid,$itemtype,$itemid,$date,$author,$anonpost,$numcomments) = $result->fields;
            $items[$id] = array(
                                'id' => $id,
                                'parent_id' => $parent_id,
                                'parent_url' => $parent_url,
                                'title' => $title,
                                'text' => $text,
                                'left_id' => $left_id,
                                'right_id' => $right_id,
                                'moduleid' => $moduleid,
                                'itemtype' => $itemtype,
                                'itemid' => $itemid,
                                'date' => $date,
                                'author' => $author,
                                'anonpost' => $anonpost,
                                'number_comments' => $numcomments,
            );
            $result->MoveNext();
        }*/
    $result->close();
    return $items;
}
