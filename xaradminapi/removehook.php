<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * delete all entries for a module - hook for ('module','remove','API')
 *
 * @param $args['objectid'] ID of the object (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_removehook($args)
{
    extract($args);

    if (empty($extrainfo)) {
        $extrainfo = [];
    }

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = 'Invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = ['objectid (module name)', 'keywords', 'adminapi', 'removehook'];
        throw new BadParameterException($vars, $msg);
    }

    $modname = $objectid;

    $modid = xarMod::getRegId($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = ['objectid (module name)', 'keywords', 'adminapi', 'removehook'];
        throw new BadParameterException($vars, $msg);
    }

    // delete all words associated with this module
    if (!xarMod::apiFunc(
        'keywords',
        'words',
        'deleteitems',
        [
            'module_id' => $modid,
        ]
    )) {
        return;
    }

    // delete all indexes for this module
    if (!xarMod::apiFunc(
        'keywords',
        'index',
        'deleteitems',
        [
            'module_id' => $modid,
        ]
    )) {
        return;
    }

    // Return the extra info
    return $extrainfo;
}
