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
 * delete all ratings items for a module - hook for ('module','remove','API')
 *
 * @param $args['itemid'] ID of the itemid (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ratings_adminapi_deleteall($args)
{
    extract($args);

    // When called via hooks, we should get the real module name from itemid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($itemid) || !is_string($itemid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'itemid (= module name)',
            'admin',
            'deleteall',
            'ratings'
        );
        throw new Exception($msg);
    }

    $modid = xarMod::getRegID($objectid);
    if (empty($modid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'module ID',
            'admin',
            'deleteall',
            'ratings'
        );
        throw new Exception($msg);
    }

    // TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurity::check('DeleteRatings')) {
        return;
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $ratingstable = $xartable['ratings'];

    $query = "DELETE FROM $ratingstable
            WHERE module_id = ?";
    $result =& $dbconn->Execute($query, array($modid));
    if (!$result) {
        return;
    }

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModHooks::call('item', 'delete', '', '');

    // TODO: delete user votes with xarModVars::delete('ratings',"$modname:$itemtype:$itemid");

    // Return the extra info
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    return $extrainfo;
}
