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
 * Called from the core when a module is removed.
 *
 * Delete the appertain comments when the module is hooked.
 */
function comments_adminapi_remove_module($args)
{
    extract($args);

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = xarML('Invalid Parameter');
        throw new BadParameterException($msg);
    }

    $modid = xarMod::getRegID($objectid);
    if (empty($modid)) {
        $msg = xarML('Invalid Parameter');
        throw new BadParameterException($msg);
        return false;
    }

    // TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    // if(!xarSecurity::check('DeleteHitcountItem',1,'Item',"All:All:$objectid")) return;

    // FIXME: we need to remove the comments for items of all types here, so a direct DB call
//        would be better than this "delete recursively" trick
    xarMod::apiFunc('comments', 'admin', 'delete_module_nodes', ['modid'=>$modid]);
    return $extrainfo;
}
