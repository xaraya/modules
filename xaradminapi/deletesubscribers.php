<?php
/**
* Delete subscribers
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
* ids or id (optional)
 */
function ebulletin_adminapi_deletesubscribers($args)
{
    // security check
    if (!xarSecurityCheck('DeleteeBulletin', 1)) return;

    extract($args);

    // accept single ID too
    if (isset($id)) $ids = array($id);

    // validate inputs
    if (!isset($ids) || !is_array($ids)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'ids', 'adminapi', 'deletesubscribers', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];

    // delete table
    $query = "DELETE FROM $substable WHERE 1 ";
    $bindvars = array();
    $query_ors = array();
    foreach ($ids as $id) {
        $query_ors[] = "$substable.xar_id = ?";
        $bindvars[] = $id;
    }
    $query .= 'AND ('.join(" OR\n", $query_ors).')';
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // call delete hooks
    $item = array();
    $item['module'] = 'ebulletin';
    $item['itemtype'] = 1;
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // success
    return true;
}

?>
