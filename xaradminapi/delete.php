<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * delete a ratings item - hook for ('item','delete','API')
 *
 * @param $args['itemid'] ID of the item
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
    if (isset($itemid)) {
        // TODO: cfr. hitcount delete stuff, once we enable item delete hooks
        // Return the extra info
        if (!isset($extrainfo)) {
            $extrainfo = [];
        }
        return $extrainfo;

    // if we're coming from the delete GUI (or elsewhere)
    } elseif (!empty($confirm)) {
        // Database information
        $dbconn = xarDB::getConn();
        $xartable =& xarDB::getTables();
        $ratingstable = $xartable['ratings'];

        $query = "DELETE FROM $ratingstable ";
        $bindvars = [];
        if (!empty($modid)) {
            if (!is_numeric($modid)) {
                $msg = xarML(
                    'Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id',
                    'admin',
                    'delete',
                    'Ratings'
                );
                throw new Exception($msg);
            }
            if (empty($itemtype) || !is_numeric($itemtype)) {
                $itemtype = 0;
            }
            $query .= " WHERE module_id = ?
                          AND itemtype = ?";
            $bindvars[] = $modid;
            $bindvars[] = $itemtype;
            if (!empty($itemid)) {
                $query .= " AND itemid = ?";
                $bindvars[] = $itemid;
            }
        }

        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }

        // TODO: delete user votes with xarModVars::delete('ratings',"$modname:$itemtype:$itemid");

        return true;
    }
    return false;
}
