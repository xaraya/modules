<?php
/**
 * Create a list type
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
 * Create a list type
 * @param $args['lid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link (optional)
 * @param $args['title'] the new title of the link (optional)
 * @param $args['url'] the new url of the link (optional)
 * @param $args['comment'] the new comment of the link (optional)
 * @param $args['sample'] sample link string (optional)
 * @param $args['name'] name of the link (optional)
 * @param $args['tid'] link type ID (optional)
 */
function lists_adminapi_createlisttype($args)
{
    // Get arguments from argument array
    extract($args);

    // Array of column set statements.
    $set = array();
    $bind = array();

    $accepted = array(
        'name' => 'type_name',
        'desc' => 'type_desc',
        'order_columns' => 'type_order_columns'
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

    // Flag as a 'type' type.
    $set[] = "xar_type";
    $bind[] = 'T';

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

    // Ensure the name is unique.
    $listtype = xarModAPIFunc(
        'lists', 'user', 'getlisttypes',
        array('type_name' => $type_name)
    );
    if (!empty($listtype)) {
        // The name is already being used.
        $msg = xarML('The list type name "#(1)" is already in use', $type_name);
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

    // We need to create two rows to get two type ids (one for list hooks
    // and one for list item hooks).
    // In the future, we may allow list types to share groups, but there
    // is no real advantage, as there should be relatively few list types.

    // Get next ID in table
    $nextId = $dbconn->GenId($table_name);
    $query = 'INSERT INTO ' . $table_name . ' (xar_tid, xar_type, xar_name) VALUES ' . '(?,\'G\',?) ';
    $result =& $dbconn->Execute($query, array($nextId, substr('group_' . $type_name, 0, 100)));
    if (!$result) {return;}
    $gid = $dbconn->PO_Insert_ID($table_name, 'xar_tid');
    $set[] = 'xar_list_type_id';
    $bind[] = $gid;

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
    $tid = $dbconn->PO_Insert_ID($table_name, 'xar_tid');

    // Call hooks to update DD etc.
    // No hooks required.

    // Let the calling process know that we have finished successfully
    return $tid;
}

?>