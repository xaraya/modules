<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * delete a ratings item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @param $args['confirm'] string coming from the delete GUI function
 * @param $args['modid'] int module id
 * @param $args['itemtype'] int itemtype
 * @param $args['itemid'] int item id
 * @return bool true on success, false on failure
 */
function ratings_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // if we're coming via a hook call
    if (isset($objectid)) {
    // TODO: cfr. hitcount delete stuff, once we enable item delete hooks
        // Return the extra info
        if (!isset($extrainfo)) {
            $extrainfo = array();
        }
        return $extrainfo;

    // if we're coming from the delete GUI (or elsewhere)
    } elseif (!empty($confirm)) {
        // Database information
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $ratingstable = $xartable['ratings'];

        $query = "DELETE FROM $ratingstable ";
        $bindvars = array();
        if (!empty($modid)) {
            if (!is_numeric($modid)) {
                $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                             'module id', 'admin', 'delete', 'Ratings');
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                                new SystemException($msg));
                return false;
            }
            if (empty($itemtype) || !is_numeric($itemtype)) {
                $itemtype = 0;
            }
            $query .= " WHERE xar_moduleid = ?
                          AND xar_itemtype = ?";
            $bindvars[] = $modid;
            $bindvars[] = $itemtype;
            if (!empty($itemid)) {
                $query .= " AND xar_itemid = ?";
                $bindvars[] = $itemid;
            }
        }

        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) return;

// TODO: delete user votes with xarModDelVar('ratings',"$modname:$itemtype:$itemid");

        return true;
    }
    return false;
}
?>
