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
 * get the ids
 *
 * @author the subitems module development team
 * @param  id $args ['objectid'] id of subitems item to get
 * @param  id $args ['itemid'] id of subitems item to get
 * @return array item array with the ids, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function subitems_userapi_dditems_getids($args)
{
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($objectid) || !isset($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'invalid count of params', 'user', 'dditems_getids', 'subitems');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $query = "SELECT xar_ddid
            FROM {$xartable['subitems_ddids']}
            WHERE xar_objectid = ? AND xar_itemid = ?";
    $result = &$dbconn->Execute($query,array($objectid, $itemid));
    if (!$result) return;

    $ids = array();
    // Check for no rows found, and if so, close the result set and return an exception
    for (; !$result->EOF; $result->MoveNext()) {
        list($ddid) = $result->fields;
        $ids[] = $ddid;
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();

    // Return the item array
    return $ids;
}

?>