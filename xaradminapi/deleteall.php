<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * delete all hitcount items for a module - hook for ('module','remove','API')
 *
 * @param $args['objectid'] ID of the object (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function hitcount_adminapi_deleteall($args)
{
    extract($args);

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID (= module name)', 'admin', 'deleteall', 'Hitcount');
        throw new Exception($msg);
    }

    $modid = xarMod::getRegId($objectid);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module ID', 'admin', 'deleteall', 'Hitcount');
        throw new Exception($msg);
    }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if(!xarSecurity::check('DeleteHitcountItem',1,'Item',"$objectid:All:All")) return;

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $hitcounttable = $xartable['hitcount'];

// FIXME: delete only for a particular module + itemtype (e.g. dd object, articles pubtype, ...)

    $query = "DELETE FROM $hitcounttable
            WHERE module_id = ?";
    $result = $dbconn->Execute($query,array((int)$modid));
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)

    // Return the extra info
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    return $extrainfo;
}

?>
