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
 * get a specific item
 *
 * @author the subitems module development team
 * @param id $args ['objectid'] id of subitems item to get
 * @param string $args['module'] Hooked module
 * @param id $args['itemtype'] ID of itemtype
 * @return array item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function subitems_userapi_ddobjectlink_get($args)
{
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($objectid) && (!isset($module) || !isset($itemtype))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'invalid count of params', 'user', 'ddobjectlink_get', 'subitems');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if(isset($objectid)) {
        $where = "xar_objectid = ?";
        $bindvars = array((int) $objectid);
    } else {
        $where = "xar_module = ? AND xar_itemtype = ?";
        $bindvars = array((string) $module, (int) $itemtype);
    }

    $query = "SELECT xar_objectid,xar_module,xar_itemtype,xar_template,xar_sort
              FROM {$xartable['subitems_ddobjects']}
              WHERE $where";
    $result = &$dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // Obtain the item information from the result set
    $items = array();
    while(!$result->EOF) {
        list($objectid, $module,$itemtype,$template,$sort) = $result->fields;

        // Create the item array
        if (empty($sort)) {
            $sort = array();
        } else {
            $sort = @unserialize($sort);
            if (!is_array($sort)) $sort = array();
        }

        $item = array(
            'objectid' => $objectid,
            'module' => $module,
            'itemtype' => $itemtype,
            'template' => $template,
            'sort' => $sort);
        $items[] = $item;
        $result->MoveNext();
    }
    $result->Close();
    // Return the items array
    return $items;
}

?>
