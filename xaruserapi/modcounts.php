<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * get the list of modules and itemtypes for the items that we're commenting on
 *
 * @param   string  status optional status to count: ALL (minus root nodes), ACTIVE, INACTIVE
 * @param integer modid optional module id you want to count for
 * @param integer itemtype optional item type you want to count for
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'comments' => $numcomments);
 */
function comments_userapi_modcounts($args)
{
    // Get arguments from argument array
    extract($args);

    // Security check
    if (!xarSecurity::check('ReadComments')) return;

    if (empty($status)) {
        $status = 'all';
    }
    $status = strtolower($status);

    // Database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $commentstable = $xartable['comments'];

    switch ($status) {
        case 'active':
            $where_status = "status = ". _COM_STATUS_ON;
            break;
        case 'inactive':
            $where_status = "status = ". _COM_STATUS_OFF;
            break;
        default:
        case 'all':
            $where_status = "status != ". _COM_STATUS_ROOT_NODE;
    }
    if (!empty($modid)) {
        $where_mod = " AND module_id = $moduleid";
        if (isset($itemtype)) {
            $where_mod .= " AND itemtype = $itemtype";
        }
    } else {
        $where_mod = '';
    }

    // Get items
    $sql = "SELECT module_id, itemtype, COUNT(*), COUNT(DISTINCT itemid)
            FROM $commentstable
            WHERE $where_status $where_mod
            GROUP BY module_id, itemtype";

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numcomments,$numitems) = $result->fields;
        if (!isset($modlist[$modid])) {
            $modlist[$modid] = array();
        }
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'comments' => $numcomments);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
