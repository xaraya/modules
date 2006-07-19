<?php
/**
 * Delete a list item
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
 * Delete a list item
 * @param $args['lid'] ID of the link
 * @return bool true on success, false on failure
 */
function lists_adminapi_deletelistitem($args)
{
    // Security check
    //if(!xarSecurityCheck('DeleteListitems')) {return;}

    // Get arguments from argument array
    extract($args);

    // TODO: Argument check
    /*
    if (!isset($lid) || !is_numeric($lid)) {
        $msg = xarML(
            'Invalid Parameter Count',
            'admin', 'delete', 'Autolinks'
        );
        xarExceptionSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }
    */

    // Check the list item exists
    $item = xarModAPIFunc(
        'lists', 'user', 'getlistitems',
        array('iid' => $iid, 'listkey' => 'index')
    );

    if (empty($item)) {
        $msg = xarML('No list item ID #(1) present', $iid);
        xarExceptionSet(
            XAR_USER_EXCEPTION,
            'MISSING_DATA',
            new DefaultUserException($msg)
        );
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table_name = $xartable['lists_items'];

    // Delete the list item
    $query = 'DELETE FROM ' . $table_name . ' WHERE xar_iid = ?';
    $result =& $dbconn->Execute($query, array($iid));
    if (!$result) {return;}

    // Let any hooks know that we have deleted a list item
    xarModCallHooks(
        'item', 'delete', $iid,
        array('itemtype' => $item[0]['tid'], 'module' => 'lists')
    );

    // Let the calling process know that we have finished successfully
    return true;
}

?>