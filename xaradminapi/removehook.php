<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * delete all entries for a module - hook for ('module','remove','API')
 *
 * @param $args['objectid'] ID of the object (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function workflow_adminapi_removehook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = [];
    }

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = ['object ID (= module name)', 'admin', 'removehook', 'workflow'];
        throw new BadParameterException($vars, $msg);
    }

    $modid = xarMod::getRegID($objectid);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = ['module ID', 'admin', 'removehook', 'workflow'];
        throw new BadParameterException($vars, $msg);
    }

    // TODO: do we delete/close all instances for this module ?

    // Return the extra info
    return $extrainfo;
}
