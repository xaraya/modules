<?php
/**
 * Create an item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Johnny Robeson
 */

/**
 * Create an item
 *
 * @param string $args[name] name of the item
 * @param string $args[alias]
 * @param int    $args[reg_user_only]
 * @param int    $args[open_direct]
 * @param int    $args[use_fixed_title]
 * @param int    $args[auto_resize]
 * @param string $args[vsize]
 * @param string $args[hsize]
 * @return int item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function window_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'window');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddWindow', 1, 'Item', "$name:All:All")) {
        return;
    }

    // Set Defaults
    if (!isset($alias)) $alias = '';
    if (!isset($reg_user_only)) $reg_user_only = xarModGetVar('window', 'reg_user_only');
    if (!isset($open_direct)) $open_direct = xarModGetVar('window', 'open_direct');
    if (!isset($use_fixed_title)) $use_fixed_title = xarModGetVar('window', 'use_fixed_title');
    if (!isset($auto_resize)) $auto_resize = xarModGetVar('window', 'auto_resize');
    if (!isset($vsize)) $vsize = xarModGetVar('window', 'vsize');
    if (!isset($hsize)) $hsize = xarModGetVar('window', 'hsize');

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $windowtable = $xartable['window'];

    $nextId = $dbconn->GenId($windowtable);

    $query = "INSERT INTO $windowtable
                    (xar_id,
                     xar_name,
                     xar_alias,
                     xar_reg_user_only,
                     xar_open_direct,
                     xar_use_fixed_title,
                     xar_auto_resize,
                     xar_vsize,
                     xar_hsize,
                     xar_status,
                     xar_label,
                     xar_description)
                    VALUES (?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?,
                            ?)";
    $bindvars = array($nextId, $name, $alias, $reg_user_only, $open_direct, $use_fixed_title,
                      $auto_resize, $vsize, $hsize, $status, $label, $description);
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $itemid = $dbconn->PO_Insert_ID($windowtable, 'xar_id');
    /*
    $item = $args;
    $item['module'] = 'example';
    $item['itemid'] = $exid;
    xarModCallHooks('item', 'create', $exid, $item);
    */
    return $itemid;
}
?>