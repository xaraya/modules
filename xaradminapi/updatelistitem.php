<?php
/**
 * Update a list item
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
 * update a list item
 * @param $args['lid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link (optional)
 * @param $args['title'] the new title of the link (optional)
 * @param $args['url'] the new url of the link (optional)
 * @param $args['comment'] the new comment of the link (optional)
 * @param $args['sample'] sample link string (optional)
 * @param $args['name'] name of the link (optional)
 * @param $args['tid'] link type ID (optional)
 * @return bool true on success
 */
function lists_adminapi_updatelistitem($args)
{
    // Get arguments from argument array
    extract($args);

    // Array of column set statements.
    $set = array();
    $bind = array();

    if (!isset($gui)) {
        $gui = false;
    }

    // String parameters.
    $accepted = array(
        'code'          =>'item_code',
        'short_name'    =>'item_short_name',
        'long_name'     =>'item_long_name',
        'desc'          =>'item_desc',
        'order'         =>'item_order',
        'lid'           =>'lid'
    );
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
    $item = xarModAPIFunc(
        'lists', 'user', 'getlistitems',
        array('iid' => $iid, 'listkey'=>'index')
    );

    if (empty($item[0])) {
        $msg = xarML('No such list item ID #(1) present', $iid);
        xarExceptionSet(
            XAR_USER_EXCEPTION, 'MISSING_DATA',
            new DefaultUserException($msg)
        );
        return;
    }

    $tid = $item[0]['tid'];

    // TODO: make sure the code is unique.

    //if (!xarSecurityCheck('EditLists')) {return;}

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_name = $xartable['lists_items'];

    // Update the link
    $query = 'UPDATE ' . $table_name . ' SET ' . $set . ' WHERE xar_iid = ?';
    $bind[] = (int)$iid;
    $result =& $dbconn->Execute($query, $bind);
    if (!$result) {return;}

    // Call hooks to update DD etc.
    xarModCallHooks(
        'item', 'update', $iid,
        array('itemtype' => $tid, 'module' => 'lists')
    );

    // Now explicitly update the DD field values if extra fields are present.
    // Only do this if not called via the GUI interface.
    if (isset($dd) && xarModIsHooked('dynamicdata', 'lists', $tid)) {
        xarModAPIfunc(
            'dynamicdata', 'admin', 'update',
            array(
                'modid' => xarModGetIDFromName('lists'),
                'itemid' => $iid,
                'itemtype' => $tid,
                'values' => $dd
            )
        );
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>