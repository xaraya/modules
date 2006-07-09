<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * delete a hitcount item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['itemtype'] optional item type for the item (not used in hook calls)
 * @param $args['modid'] int module id
 * @param $args['itemtype'] int itemtype
 * @param $args['itemid'] int item id
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function hitcount_adminapi_delete($args)
{
    extract($args);

    // if we're coming via a hook call
    if (isset($objectid)) {
        if (!is_numeric($objectid)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                        'object ID', 'admin', 'delete', 'Hitcount');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                            new SystemException($msg));
            return false;
        }
        $itemid = $objectid;

        // When called via hooks, modname will be empty, but we get it from the
        // extrainfo or from the current module
        if (empty($modname) || !is_string($modname)) {
            if (isset($extrainfo) && is_array($extrainfo) &&
                isset($extrainfo['module']) && is_string($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            } else {
                $modname = xarModGetName();
            }
        }
        $modid = xarModGetIDFromName($modname);
        if (empty($modid)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                        'module name', 'admin', 'delete', 'Hitcount');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return false;
        }

        if (!isset($itemtype) || !is_numeric($itemtype)) {
            if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
                $itemtype = $extrainfo['itemtype'];
            } else {
                $itemtype = 0;
            }
        }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
        if(!xarSecurityCheck('DeleteHitcountItem',1,'Item',"$modname:$itemtype:$itemid")) return;

        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $hitcounttable = $xartable['hitcount'];

        // Don't bother looking if the item exists here...
        $query = "DELETE FROM $hitcounttable
                WHERE xar_moduleid = ?
                  AND xar_itemtype = ?
                  AND xar_itemid = ?";
        $bindvars = array((int)$modid, (int)$itemtype, (int)$itemid);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)

        // Return the extra info
        if (!isset($extrainfo)) {
            $extrainfo = array();
        }
        return $extrainfo;

    // if we're coming from the delete GUI (or elsewhere)
    } elseif (!empty($confirm)) {

        if (!xarSecurityCheck('AdminHitcount')) return;

        // Database information
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $hitcounttable = $xartable['hitcount'];

        $bindvars = array();
        $query = "DELETE FROM $hitcounttable ";
        if (!empty($modid)) {
            if (!is_numeric($modid)) {
                $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                             'module id', 'admin', 'delete', 'Hitcount');
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                new SystemException($msg));
                return false;
            }
            if (empty($itemtype) || !is_numeric($itemtype)) {
                $itemtype = 0;
            }
            $query .= " WHERE xar_moduleid = ?
                          AND xar_itemtype = ?";
            $bindvars[] = (int) $modid;
            $bindvars[] = (int) $itemtype;
            if (!empty($itemid)) {
                $query .= " AND xar_itemid = ?";
                $bindvars[] = (int) $itemid;
            }
        }

        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;

        return true;
    }
    return false;
}

?>
