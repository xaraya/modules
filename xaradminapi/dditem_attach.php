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
 * create a new subitems item
 *
 * @author the subitems module development team
 * @param ID $args['objectid'] name of the item
 * @param ID $args['itemid'] number of the item
 * @param ID $args['ddid'] ID of the Dynamic Data object
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, DATABASE_ERROR
 */
function subitems_adminapi_dditem_attach($args)
{
    extract($args);

    $invalid = array();
    if (!isset($objectid) ||!is_numeric($objectid))
        // The subobject
        $invalid[] = 'objectid';
    if (!isset($itemid) || !is_numeric($itemid))
        // The itemid of the paren
        $invalid[] = 'itemid';
    if (!isset($ddid) || !is_numeric($ddid))
        // The id of the subitem created which should be attached
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

    $query = "INSERT INTO {$xartable['subitems_ddids']}
                (xar_objectid, xar_itemid, xar_ddid)
              VALUES (?,?,?)";
    $result = &$dbconn->Execute($query,array($objectid, $itemid, $ddid));
    if (!$result) return;

    // Return the id of the newly created item to the calling process
    return true;
}

?>