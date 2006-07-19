<?php
/**
 * Update a list
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
 * update a list
 * @param $args['lid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link (optional)
 * @param $args['title'] the new title of the link (optional)
 * @param $args['url'] the new url of the link (optional)
 * @param $args['comment'] the new comment of the link (optional)
 * @param $args['sample'] sample link string (optional)
 * @param $args['name'] name of the link (optional)
 * @param $args['tid'] link type ID (optional)
 */
function lists_adminapi_updatelist($args)
{
    // Get arguments from argument array
    extract($args);
//var_dump($args); die;
    // Array of column set statements.
    $set = array();
    $bind = array();

    $accepted = array(
        'name' => 'list_name',
        'desc' => 'list_desc',
        'order_columns' => 'list_order_columns',
        'list_type_id' => 'tid'
    );

    // String parameters.
    foreach($accepted as $colname => $parameter)
    {
        if (isset($$parameter))
        {
            $set[] = "xar_$colname = ?";
            $bind[] = $$parameter;
        }
    }

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
    // Create the 'set' statement.
    $set = implode(', ', $set);

    // Ensure the list exists.
    $list = xarModAPIFunc(
        'lists', 'user', 'getlists',
        array('lid' => $lid)
    );

    if (empty($list)) {
        $msg = xarML('No such list ID #(1) present', $lid);
        xarExceptionSet(
            XAR_USER_EXCEPTION, 'MISSING_DATA',
            new DefaultUserException($msg)
        );
        return;
    }

    // TODO: make sure the name is unique.

    //if (!xarSecurityCheck('EditLists')) {return;}

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_name = $xartable['lists_types'];

    // Update the link
    $query = 'UPDATE ' . $table_name . ' SET ' . $set . ' WHERE xar_tid = ?';
    $bind[] = (int)$lid;
    $result =& $dbconn->Execute($query, $bind);
    if (!$result) {return;}

    // Call hooks to update DD etc.
    xarModCallHooks(
        'item', 'update', $lid,
        array('itemtype' => $list[$lid]['type_group_id'], 'module' => 'lists')
    );

    // Now explicitly update the DD field values if extra fields are present.
    // Only do this if not called via the GUI interface.
    if (isset($dd) && xarModIsHooked('dynamicdata', 'lists', $list[$lid]['type_group_id'])) {
        xarModAPIfunc(
            'dynamicdata', 'admin', 'update',
            array(
                'modid' => xarModGetIDFromName('lists'),
                'itemid' => $lid,
                'itemtype' => $list[$lid]['type_group_id'],
                'values' => $dd
            )
        ); //var_dump($dd); echo " itemtype=".$list[$lid]['type_group_id']." lid=$lid ";
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>