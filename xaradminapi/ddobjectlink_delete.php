<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * delete a subitems item
 *
 * @author the subitems module development team
 * @param  id $args ['objectid'] ID of the item or
 * @param id $args['modid']
 * @param id $args['itemtype']
 * @return bool true on success of deletion, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function subitems_adminapi_ddobjectlink_delete($args)
{
    extract($args);

    if (!isset($objectid) && (!isset($modid) || !isset($itemtype)))     {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'invalid count', 'admin', 'delete', 'subitems');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('subitems','user', 'ddobjectlink_get', $args);
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $item = $item[0];

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $query = "DELETE FROM {$xartable['subitems_ddobjects']}
            WHERE xar_objectid = ?";
    $result = &$dbconn->Execute($query, array($item['objectid']));
    if (!$result) return;

    $item['module'] = 'subitems';
    $item['itemid'] = $objectid;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'delete', $objectid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>