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
sys::import('modules.comments.xarincludes.defines');

/**
 * Count comments by modid/objectid/all and active/inactive/all
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   string  type     What to gather for: ALL, MODULE, or OBJECT (object == modid/objectid pair)
 * @param   string  status   What status' to count: ALL (minus root nodes), ACTIVE, INACTIVE
 * @param   integer modid    Module to gather info on (only used with type == module|object)
 * @param   integer itemtype Item type in that module to gather info on (only used with type == module|object)
 * @param   integer objectid ObjectId to gather info on (only used with type == object)
 * @returns integer total comments
 */
function comments_adminapi_count_comments($args)
{
    extract($args);
    $dbconn         = xarDB::getConn();
    $xartable       =& xarDB::getTables();
    $total          = 0;
    $status         = strtolower($status);
    $type           = strtolower($type);
    $where_type     = '';
    $where_status   = '';

    if (empty($type) || !eregi('^(all|module|object)$', $type)) {
        $msg = xarML('Invalid Parameter \'type\' to function count_comments(). \'type\' must be: all, module, or object.');
        throw new BadParameterException($msg);
    } else {
        switch ($type) {
            case 'object':
                if (empty($objectid)) {
                    $msg = xarML('Missing or Invalid Parameter \'objectid\'');
                    throw new BadParameterException($msg);
                }

                $where_type = "objectid = '$objectid' AND ";

                // Allow the switch to fall through if type == object because
                // we need modid for object in addition to objectid
                // hence, no break statement here :-)

                // no break
            case 'module':
                if (empty($modid)) {
                    $msg = xarML('Missing or Invalid Parameter \'modid\'');
                    throw new BadParameterException($msg);
                }

                $where_type .= "modid = $modid";

                if (isset($itemtype) && is_numeric($itemtype)) {
                    $where_type .= " AND itemtype = $itemtype";
                }
                break;

            default:
            case 'all':
                $where_type = "1";
        }
    }
    if (empty($status) || !eregi('^(all|inactive|active)$', $status)) {
        $msg = xarML('Invalid Parameter \'status\' to function count_module_comments(). \'status\' must be: all, active, or inactive.');
        throw new BadParameterException($msg);
    } else {
        switch ($status) {
            case 'active':
                $where_status = "status = ". _COM_STATUS_ON;
                break;
            case 'inactive':
                $where_status = "status = ". _COM_STATUS_OFF;
                break;
            default:
            case 'active':
                $where_status = "status != ". _COM_STATUS_ROOT_NODE;
        }
    }
    $query = "SELECT COUNT(id)
                FROM $xartable[comments]
               WHERE $where_type
                 AND $where_status";
    $result =& $dbconn->Execute($query);
    if (!$result) {
        return;
    }

    if ($result->EOF) {
        return 0;
    }
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
