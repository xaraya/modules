<?php
/**
* Delete an issue
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
 * delete an issue
 *
 * @author the eBulletin module development team
 * @param  $args ['iid'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_adminapi_deleteissue($args)
{
    extract($args);

    // validate inputs
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'id', 'adminapi', 'deleteissue', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // get publication
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('DeleteeBulletin', 1, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $issuestable = $xartable['ebulletin_issues'];

    // delete table
    $query = "DELETE FROM $issuestable WHERE xar_id = ?";
    $result = $dbconn->Execute($query, array($id));
    if (!$result) return;

    // call delete hooks
    $item = $issue;
    $item['module'] = 'ebulletin';
    $item['itemtype'] = 1;
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // success
    return true;
}

?>
