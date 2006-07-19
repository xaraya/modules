<?php
/**
 * Create a list
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Create a list
 * @param $args['lid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link (optional)
 * @param $args['title'] the new title of the link (optional)
 * @param $args['url'] the new url of the link (optional)
 * @param $args['comment'] the new comment of the link (optional)
 * @param $args['sample'] sample link string (optional)
 * @param $args['name'] name of the link (optional)
 * @param $args['tid'] link type ID (optional)
 */
function lists_adminapi_createlist($args)
{
    // Get arguments from argument array
    extract($args);

    // Array of column set statements.
    $set = array();
    $bind = array();

    $accepted = array(
        'name' => 'list_name',
        'desc' => 'list_desc',
        'order_columns' => 'list_order_columns',
        'list_type_id' => 'tid'
    );

    // Build arrays of columns and values to insert.
    foreach($accepted as $colname => $parameter)
    {
        if (isset($args[$parameter]))
        {
            $set[] = "xar_$colname";
            $bind[] = $args[$parameter];
        }
    }

    // Flag as a 'list' type.
    $set[] = "xar_type";
    $bind[] = 'L';

    // TODO: Argument check
/*
    if (!isset($tid) || empty($set)) {
        $msg = xarML(
            'Invalid Parameter Count',
            join(', ', $args)
        );
        xarExceptionSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }
*/

    // Ensure the list type exists.
    $listtype = xarModAPIFunc(
        'lists', 'user', 'getlisttypes',
        array('tid' => $tid)
    );
    if (empty($listtype)) {
        // The list type does not exist.
        $msg = xarML('The list type ID "#(1)" does not exist', $tid);
        xarExceptionSet(
            XAR_USER_EXCEPTION, 'MISSING_DATA',
            new DefaultUserException($msg)
        );
        return;
    }

    // Ensure the name is unique.
    $list = xarModAPIFunc(
        'lists', 'user', 'getlists',
        array('tid' => $tid, 'list_name' => $list_name)
    );
    if (!empty($list)) {
        // The name is already being used.
        $msg = xarML('The list name "#(1)" is already in use', $list_name);
        xarExceptionSet(
            XAR_USER_EXCEPTION, 'BAD_PARAM',
            new DefaultUserException($msg)
        );
        return;
    }

    //if (!xarSecurityCheck('EditLists')) {return;}

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_name = $xartable['lists_types'];

    // Get next ID in table
    $nextId = $dbconn->GenId($table_name);

    // Add the ID column to the list of columns.
    array_unshift($set, 'xar_tid');
    array_unshift($bind, $nextId);

    // Create the 'insert' statement.
    $val = implode(', ', array_pad(array(), count($set), '?'));
    $set = implode(', ', $set);
    $query = 'INSERT INTO ' . $table_name . ' ('.$set.') VALUES ' . '('.$val.') ';

    $result =& $dbconn->Execute($query, $bind);
    if (!$result) {return;}

    // Get the ID of the item created.
    $lid = $dbconn->PO_Insert_ID($table_name, 'xar_tid');

    // Call hooks to update DD etc.
    xarModCallHooks(
        'item', 'create', $lid,
        array(
            'itemtype' => $listtype[$tid]['type_group_id'],
            'module' => 'lists',
            'urlparam' => 'lid'
        )
    );

    // Update explicit DD fields if necessary
    if (isset($dd) && xarModIsHooked('dynamicdata', 'lists', $listtype[$tid]['type_group_id'])) {
        xarModAPIfunc(
            'dynamicdata', 'admin', 'update',
            array(
                'modid' => xarModGetIDFromName('lists'),
                'itemid' => $lid,
                'itemtype' => $listtype[$tid]['type_group_id'],
                'values' => $dd
            )
        );
    }

    // Let the calling process know that we have finished successfully
    return $lid;
}

?>