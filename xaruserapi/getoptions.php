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
 * 3. Module Defaults
 * 4. internal defaults
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @returns array list of viewing options (depth, render style, order, and sortby)
 */
function comments_userapi_getoptions($args)
{
    if (!xarVarFetch('depth', 'int', $settings['depth'], NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('render', 'str', $settings['render'], NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'int', $settings['order'], NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'int', $settings['sortby'], NULL, XARVAR_NOT_REQUIRED)) return;
    
    $args['modname'] = xarModGetNameFromID($args['modid']);

    // if one of the settings is configured, then all should be.
    // Render settings
    foreach ($settings as $k => $v) {
        if (!is_null($v)) continue;
        if (xarUserIsLoggedIn() && xarModGetVar('comments','setuserrendering')) {
            $settings[$k] = xarModGetUserVar('comments',$k);
            continue;
        }
        
        $hookedvar = xarModGetVar($args['modname'],$k . '.' .$args['itemtype']);
        if (!empty($hookedvar)) {
            $settings[$k] = $hookedvar;
            continue;
        }
        $settings[$k] = xarModGetVar('comments',$k);
    }

    $showoptions = xarModGetVar($args['modname'], 'showoptions' . '.' .$args['itemtype']);
    $anonpost = xarModGetVar($args['modname'], 'AllowPostAsAnon' . '.' .$args['itemtype']);
    $edittimelimit = xarModGetVar($args['modname'], 'edittimelimit' . '.' .$args['itemtype']);
    $settings['showoptions'] = !is_null($showoptions) ? $showoptions : xarModGetVar('comments','showoptions');
    $settings['AllowPostAsAnon'] = !is_null($anonpost) ? $anonpost : xarModGetVar('comments','AllowPostAsAnon');
    $settings['edittimelimit'] = !is_null($edittimelimit) ? $edittimelimit : xarModGetVar('comments','edittimelimit');

    //die(var_dump($settings));
    return $settings;
}
?>
