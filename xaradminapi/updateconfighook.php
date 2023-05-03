<?php
/**
 * Update hooks configuration for a module / itemtype
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tracker Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tracker Module Development Team
 */
/**
 * Generate the common menu configuration
 * The complete explanation for generating menu items is at example/xaradminapi/menu.php
 *
 * @author the Tracker module development team
 */
function crispbb_adminapi_updateconfighook($args)
{

    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    // only forum admins see the modifyconfighook
    // don't throw an error here, life in hooks goes on...
    if (!xarSecurity::check('AdminCrispBB', 0)) return $extrainfo;

    if (empty($extrainfo['module'])) {
        $modname = xarMod::getName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'hooksapi', 'updateconfig', 'tracker');
        throw new BadParameterException($vars, $msg);
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo) && is_array($extrainfo) &&
             isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    $settings = array();

    if (!xarVar::fetch('crispbb_fid', 'id', $settings['fid'], NULL, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('crispbb_postsperpage', 'int:0:100', $settings['postsperpage'], 0, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('crispbb_quickreply', 'checkbox', $settings['quickreply'], false, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('crispbb_newaction', 'int:0:1', $settings['newaction'], 0, xarVar::NOT_REQUIRED)) return;

    $var_to_look_for = $modname;
    if (!empty($itemtype)) {
        $var_to_look_for .= '_' . $itemtype;
    }
    $var_to_look_for .= '_hooks';

    xarModVars::set('crispbb', $var_to_look_for, serialize($settings));

    return $extrainfo;
}
?>