<?php

/**
 * File: $Id: s.xaradminapi.php 1.26 03/10/21 02:05:20+02:00 mikespub@fully.qualified.hostname $
 *
 * Comments administration API functions
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

include_once('modules/comments/xarincludes/defines.php');

function comments_adminapi_updateconfig($args) {}

/**
 * utility function pass individual menu items to the main menu
 *
 * @author the Example module development team
 * @returns array
 * @return array containing the menulinks for the main menu items.
 */
function comments_adminapi_getmenulinks()
{

// Security Check
        $menulinks[] = Array('url'   => xarModURL('comments',
                                                  'admin',
                                                  'main'),
                              'title' => xarML('An Overview of the Comments Module'),
                              'label' => xarML('Overview'));

        $menulinks[] = Array('url'   => xarModURL('comments',
                                                  'admin',
                                                  'stats'),
                             'title' => xarML('View comments per module statistics'),
                             'label' => xarML('View Statistics'));

        $menulinks[] = Array('url'   => xarModURL('comments',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the comments module configuration'),
                             'label' => xarML('Modify Config'));
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

/* replaced by getmodules
function comments_adminapi_get_stats( ) {

    $modules = xarModAPIFunc('comments','admin','get_module_list');

    if (!count($modules) || empty($modules)) {
        return array();
    }

    foreach ($modules as $modid => $list) {
        $modules[$modid]['pages']    = xarModAPIFunc('comments','admin','count_module_pages',
                                            array('modid'=>$modid));
        $modules[$modid]['total']    = xarModAPIFunc('comments','admin','count_comments',
                                            array('type'    => 'module',
                                                  'modid'   => $modid,
                                                  'status'  => 'all'));

        $modules[$modid]['active']   = xarModAPIFunc('comments','admin','count_comments',
                                            array('type'    => 'module',
                                                  'modid'   => $modid,
                                                  'status'  => 'active'));

        $modules[$modid]['inactive'] = xarModAPIFunc('comments','admin','count_comments',
                                            array('type'    => 'module',
                                                  'modid'   => $modid,
                                                  'status'  => 'inactive'));
    }

    return $modules;
}
*/

/* replaced by getitems
function comments_adminapi_get_module_stats( $args ) {

    extract($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Invalid or Missing Parameter \'modid\'');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    xarModAPILoad('comments','user');
    $pages = xarModAPIFunc('comments','user','get_object_list',
                            array( 'modid'    => $modid, 
                                   'itemtype' => $itemtype ));

    if (!count($pages) || empty($pages)) {
        return array();
    }

    foreach ($pages as $pageid => $list) {
        $pages[$pageid]['total']    = xarModAPIFunc('comments','admin','count_comments',
                                            array('type'    => 'object',
                                                  'modid'   => $modid,
                                                  'itemtype'=> $itemtype,
                                                  'objectid'=> $pageid,
                                                  'status'  => 'all'));

        $pages[$pageid]['inactive'] = xarModAPIFunc('comments','admin','count_comments',
                                            array('type'    => 'object',
                                                  'modid'   => $modid,
                                                  'itemtype'=> $itemtype,
                                                  'objectid'=> $pageid,
                                                  'status'  => 'inactive'));
    }

    return $pages;

}
*/

/* replaced by getmodules
function comments_adminapi_get_module_list( ) {

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];
    $mtable = &$xartable['modules'];

    $sql     = "SELECT DISTINCT $xartable[modules].xar_name AS modname, $ctable[modid] AS modid
                           FROM $xartable[comments]
                      LEFT JOIN $xartable[modules]
                             ON $ctable[modid] = $xartable[modules].xar_regid
                       ORDER BY modname ASC";


    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if it's an empty set, return array()
    if ($result->EOF) {
        return array();
    }

    // zip through the list of results and
    // create the return array
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $ret[$row['modid']]['modname'] = $row['modname'];
        $result->MoveNext();
    }
    $result->Close();

    return $ret;

}
*/

/* replaced by getitems
function comments_adminapi_count_module_pages( $args ) {
    extract($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid modid!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // initialize total to zero
    $total = 0;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $query = "SELECT DISTINCT $ctable[objectid]
                         FROM $xartable[comments]
                        WHERE $ctable[modid] = $modid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // zip through the list of results and
    // add them up to create our total
    while (!$result->EOF) {
        $total++;
        $result->MoveNext();
    }
    $result->Close();

    return $total;

}
*/

/**
 * Delete a node from the tree and reassign it's children to it's parent
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $node   the id of the node to delete
 * @param   integer     $pid    the deletion node's parent id (used to reassign the children)
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_node( $args ) {

    extract($args);

    if (empty($node)) {
        $msg = xarML('Missing or Invalid comment id!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($pid)) {
        $msg = xarML('Missing or Invalid parent id!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Grab the deletion node's left and right values
    // as well as the max right value for the comments table
    $del_node_lr = xarModAPIFunc('comments','user','get_node_lrvalues',array('cid'=>$node));
    $max_right = xarModAPIFunc('comments','user','get_table_maxright');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // delete the node
    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  $ctable[cid]='$node'";

    // reset all parent id's == deletion node's id to that of
    // the deletion node's parent id
    $sql2 = "UPDATE $xartable[comments]
                SET $ctable[pid]='$pid'
              WHERE $ctable[pid]='$node'";

    if (!$dbconn->Execute($sql))
        return;

    if (!$dbconn->Execute($sql2))
        return;

    // Go through and fix all the l/r values for the comments
    // First we subtract 1 from all the deletion node's children's left and right values
    // and then we subtract 2 from all the nodes > the deletion node's right value
    // and <= the max right value for the table
    xarModAPIFunc('comments','user','remove_gap',array('startpoint' => $del_node_lr['xar_left'], 
                                                       'endpoint'   => $del_node_lr['xar_right'],
                                                       'gapsize'    => 1));
    xarModAPIFunc('comments','user','remove_gap',array('startpoint' => $del_node_lr['xar_right'],
                                                       'endpoint'   => $max_right, 
                                                       'gapsize'    => 2));

    return $dbconn->Affected_Rows();
}

/**
 * Delete a branch from the tree
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $node   the id of the node to delete
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_branch( $args ) {

    extract($args);

    if (empty($node)) {
        $msg = xarML('Invalid or Missing Parameter \'node\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    xarModAPILoad('comments','user');

    // Grab the deletion node's left and right values
    // as well as the max right value for the comments table
    $del_node_lr = xarModAPIFunc('comments','user','get_node_lrvalues', array('cid'=>$node));
    $max_right = xarModAPIFunc('comments','user','get_table_maxright');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    $sql = "DELETE
              FROM  $xartable[comments]
             WHERE  $ctable[left]   >= $del_node_lr[xar_left]
               AND  $ctable[right]  <= $del_node_lr[xar_right]";

    $result =& $dbconn->Execute($sql);

    if (!$dbconn->Affected_Rows()) {
        return FALSE;
    }

    // figure out the adjustment value for realigning the left and right
    // values of all the comments
    $adjust_value = (($del_node_lr['xar_right'] - $del_node_lr['xar_left']) + 1);


    // Go through and fix all the l/r values for the comments
    if (xarModAPIFunc('comments','user','remove_gap', array('startpoint' =>$del_node_lr['xar_left'], 
                                                            'endpoint'    =>$max_right,
                                                            'gapsize'   => $adjust_value)))
    {
        return $dbconn->Affected_Rows();
    }

}

/**
 * Delete all comments attached to the specified objectid / modid pair
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the comments are associated with
 * @param   integer     $modid      the item type that the comments are associated with
 * @param   integer     $objectid   the id of the object within the specified module that the comments are attached to
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_object_nodes( $args ) {
    extract($args);

    if (empty($objectid)) {
        $msg = xarML('Missing or Invalid parameter \'objectid\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    // Grab the root node's values for the specified modid/objectid combo
    $args = array('modid'    => $modid, 
                  'objectid' => $objectid, 
                  'itemtype' => $itemtype);
    $root_node = xarModAPIFunc('comments','user','get_node_root', $args);

    // Delete the entire branch in the tree for this objectid
    return xarModAPIFunc('comments','admin','delete_branch',
                          array('node' => $root_node['xar_cid']));

}

/**
 * Delete all comments attached to the specified module id
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the comments are associated with
 * @param   integer     $itemtype   the item type that the comments are associated with
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_module_nodes( $args ) {
    extract($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    $return_value = TRUE;

    $pages = xarModAPIFunc('comments','user','get_object_list', 
                            array('modid' => $modid, 
                                  'itemtype' => $itemtype ));

    if (count($pages) <= 0 || empty($pages)) {
        return $return_value;
    } else {
        foreach ($pages as $object) {
            xarModAPIFunc('comments','admin','delete_object_nodes',
                          array('modid'     => $modid,
                                'itemtype'  => $itemtype,
                                'objectid'  => $object['pageid']));
        }
    }
    return $return_value;
}

/**
 * Count comments by modid/objectid/all and active/inactive/all
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   string  type     What to gather for: ALL, MODULE, or OBJECT (object == modid/objectid pair)
 * @param   string  status   What status' to count: ALL (minus root nodes), ACTIVE, INACTIVE
 * @param   integer modid    Module to gather info on (only used with type == module|object)
 * @param   integer itemtype Item type in that module to gather info on (only used with type == module|object)
 * @param   integer objectid ObjectId to gather info on (only used with type == object)
 * @returns integer total comments
 */
function comments_adminapi_count_comments( $args ) {

    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $total          = 0;
    $status         = strtolower($status);
    $type           = strtolower($type);
    $where_type     = '';
    $where_status   = '';

    if (empty($type) || !eregi('^(all|module|object)$',$type)) {
        $msg = xarML('Invalid Parameter \'type\' to function count_comments(). \'type\' must be: all, module, or object.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } else {

        switch ($type) {
            case 'object':
                if (empty($objectid)) {
                    $msg = xarML('Missing or Invalid Parameter \'objectid\'');
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return;
                }

                $where_type = "$ctable[objectid] = '$objectid' AND ";

                // Allow the switch to fall through if type == object because
                // we need modid for object in addition to objectid
                // hence, no break statement here :-)

            case 'module':
                if (empty($modid)) {
                    $msg = xarML('Missing or Invalid Parameter \'modid\'');
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return;
                }

                $where_type .= "$ctable[modid] = '$modid'";

                if (isset($itemtype) && is_numeric($itemtype)) {
                    $where_type .= " AND $ctable[itemtype] = '$itemtype'";
                }
                break;

            default:
            case 'all':
                $where_type = "1";
        }
    }

    if (empty($status) || !eregi('^(all|inactive|active)$',$status)) {
        $msg = xarML('Invalid Parameter \'status\' to function count_module_comments(). \'status\' must be: all, active, or inactive.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } else {
        switch ($status) {
            case 'active':
                $where_status = "$ctable[status] = '". _COM_STATUS_ON ."'";
                break;
            case 'inactive':
                $where_status = "$ctable[status] = '". _COM_STATUS_OFF ."'";
                break;
            default:
            case 'active':
                $where_status = "$ctable[status] != '". _COM_STATUS_ROOT_NODE ."'";
        }
    }

    $query = "SELECT COUNT($ctable[cid])
                FROM $xartable[comments]
               WHERE $where_type
                 AND $where_status";

    $result =& $dbconn->Execute($query);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;

}


/**
 * Called from the core when a module is removed.
 *
 * Delete the appertain comments when the module is hooked.
 */
function comments_adminapi_remove_module( $args ) {
    extract($args);

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID (= module name)', 'admin', 'remove_module', 'Comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $modid = xarModGetIDFromName($objectid);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module ID', 'admin', 'deleteall', 'Hitcount');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    // if(!xarSecurityCheck('DeleteHitcountItem',1,'Item',"All:All:$objectid")) return;

// FIXME: we need to remove the comments for items of all types here, so a direct DB call
//        would be better than this "delete recursively" trick
    xarModAPIFunc('comments','admin','delete_module_nodes',array('modid'=>$modid));

    return $extrainfo;

}

?>
