<?php
/**
* Delete a publication
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * delete an ebulletin item
 *
 * @author the eBulletin module development team
 * @param  $args ['id'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_adminapi_delete($args)
{
    extract($args);

    // validate inputs
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'id', 'adminapi', 'delete', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $id));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('DeleteeBulletin', 1, 'Publication', "$pub[name]:$id")) return;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubstable = $xartable['ebulletin'];

    // delete table
    $query = "DELETE FROM $pubstable WHERE xar_id = ?";
    $result = $dbconn->Execute($query, array($id));
    if (!$result) return;

    // call delete hooks
    $item = $pub;
    $item['module'] = 'ebulletin';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // success
    return true;
}

?>
