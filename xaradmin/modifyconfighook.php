<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Modify Configuration of hooked module
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  array $args an array of arguments (if called by other modules)
 * @return array $data The array that contains all data for the template
 */
function crispbb_admin_modifyconfighook($args)
{
    // only forum admins see the modifyconfighook
    // don't throw an error here, life in hooks goes on...
    if (!xarSecurity::check('AdminCrispBB', 0)) return;

    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    if (empty($modname)) {
        if (empty($extrainfo['module'])) {
            $modname = xarMod::getName();
        } else {
            $modname = $extrainfo['module'];
        }
    }

    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'admin', 'modifyconfighook', 'crispBB');
        // throw new BadParameterException($vars, $msg);
        // don't throw an error here, life in hooks goes on...
        return;
    }

    if (empty($itemtype)) {
        $itemtype = 0;
        if (isset($extrainfo['itemtype'])) {
            $itemtype = $extrainfo['itemtype'];
        }
    }

    $var_to_look_for = $modname;
    if (!empty($itemtype)) {
        $var_to_look_for .= '_' . $itemtype;
    }
    $var_to_look_for .= '_hooks';
    $string = xarModVars::get('crispbb', $var_to_look_for);
    if (empty($string) || !is_string($string)) {
        $string = xarModVars::get('crispbb', 'crispbb_hooks');
    }
    $settings = !empty($string) && is_string($string) ? unserialize($string) : array();

    $data = array();
    $data['fid'] = !empty($settings['fid']) ? $settings['fid'] : NULL;
    $data['postsperpage'] = isset($settings['postsperpage']) ? $settings['postsperpage'] : 0;
    $data['quickreply'] = isset($settings['quickreply']) ? $settings['quickreply'] : false;

    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums', array('privcheck' => true, 'ftype' => 0));

    // no privs (shouldn't happen), or no forums (might)
    if (isset($forums['NO_PRIVILEGES']) || isset($forums['BAD_DATA'])) return;

    $foptions = array();
    foreach ($forums as $forum) {
        $foptions[$forum['fid']] = array('id' => $forum['fid'], 'name' => $forum['transformed_fname']);
    }
    $data['foptions'] = $foptions;
    return xarTpl::module('crispbb', 'admin', 'modifyconfighook', $data);
}
?>