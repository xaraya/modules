<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Grabs the list of viewing options in the following order of precedence:
 * 1. POST/GET
 * 2. User Settings (if user is logged in)
 * 3. Module Defaults (if args() is given)
 * 4. System Defaults
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param array args OPTIONAL  
 * @returns array list of viewing options (depth, render style, order, and sortby)
 */
function comments_userapi_getoptions($args)
{
    // Get from POST/GET variables and fallback to system defaults
    if (!xarVarFetch('depth', 'int', $settings['depth'], xarModGetVar('comments','depth'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('render', 'str', $settings['render'], xarModGetVar('comments','render'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'int', $settings['order'], xarModGetVar('comments','order'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'int', $settings['sortby'], xarModGetVar('comments','sortby'), XARVAR_NOT_REQUIRED)) return;

    // Get from User Settings
    if (xarUserIsLoggedIn() && xarModGetVar('comments','usersetrendering')) {
        foreach ($settings as $k => $v) {
            $uservar = xarModGetUserVar('comments', $k);
            if (!empty($uservar)) {
              $settings[$k] = $uservar;
            }
        }
    }

    // Get from hooked module if $args given    
    if (isset($args['modid'])) {
        $args['modname'] = xarModGetNameFromID($args['modid']);
        foreach ($settings as $k => $v) {
            $hookedvar = xarModGetVar($args['modname'],$k . '.' .$args['itemtype']);
            if (!empty($hookedvar)) {
                $settings[$k] = $hookedvar;
            }
        }
        $showoptions = xarModGetVar($args['modname'], 'showoptions' . '.' .$args['itemtype']);
        $anonpost = xarModGetVar($args['modname'], 'AllowPostAsAnon' . '.' .$args['itemtype']);
        $edittimelimit = xarModGetVar($args['modname'], 'edittimelimit' . '.' .$args['itemtype']);
        // Get non view options from system default if not set in hooked module
        $settings['showoptions'] = !is_null($showoptions) ? $showoptions : xarModGetVar('comments','showoptions');
        $settings['AllowPostAsAnon'] = !is_null($anonpost) ? $anonpost : xarModGetVar('comments','AllowPostAsAnon');
        $settings['edittimelimit'] = !is_null($edittimelimit) ? $edittimelimit : xarModGetVar('comments','edittimelimit');

    } else {
        $settings['showoptions'] = xarModGetVar('comments','showoptions');
        $settings['AllowPostAsAnon'] = xarModGetVar('comments','AllowPostAsAnon');
        $settings['edittimelimit'] = xarModGetVar('comments','edittimelimit');
    }

    return $settings;
}
?>
