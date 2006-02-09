<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * create a new subitems item
 *
 * @author the subitems module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return subitems item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function subitems_adminapi_dditem_detach($args)
{
    extract($args);

    $invalid = array();
    if (!isset($objectid) ||!is_numeric($objectid))
        $invalid[] = 'objectid';
    if (!isset($ddid) || !is_numeric($ddid))
        $invalid[] = 'ddid';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'subitems');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $query = "DELETE FROM {$xartable['subitems_ddids']}
        WHERE xar_objectid = ? AND xar_ddid = ?";
    $result = &$dbconn->Execute($query, array($objectid, $ddid));
    if (!$result) return;

    // Return the id of the newly created item to the calling process
    return true;
}

?>