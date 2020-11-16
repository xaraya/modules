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
function comments_userapi_getoptions()
{
    if (!xarVarFetch('depth', 'int', $depth, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('render', 'str', $render, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('order', 'int', $order, null, XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('sortby', 'int', $sortby, null, XARVAR_NOT_REQUIRED)) {
        return;
    }

    // if one of the settings configured, the all should be.
    // Order of precedence for determining which
    // settings to use.  (User_Defined is (obviously)
    // dependant on the user being logged in.):
    // Get/Post->[user_defined->]admin_defined

    if (isset($depth)) {
        if ($depth == 0) {
            $settings['depth'] = 1;
        } else {
            $settings['depth'] = $depth;
        }
    } else {
        // Not doing user settings for now
        /*if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['depth'] = xarModUserVars::get('comments','depth');
        } else {*/
        $settings['depth'] = xarModVars::get('comments', 'depth');
        /*}*/
    }

    if (isset($render) && !empty($render)) {
        $settings['render'] = $render;
    } else {
        /*if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['render'] = xarModUserVars::get('comments','render');
        } else {*/
        $settings['render'] = xarModVars::get('comments', 'render');
        /*}*/
    }

    if (isset($order) && !empty($order)) {
        $settings['order'] = $order;
    } else {
        /*if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['order'] = xarModUserVars::get('comments','order');
        } else {*/
        $settings['order'] = xarModVars::get('comments', 'order');
        /*}*/
    }

    if (isset($sortby) && !empty($sortby)) {
        $settings['sortby'] = $sortby;
    } else {
        /*if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['sortby'] = xarModUserVars::get('comments','sortby');
        } else {*/
        $settings['sortby'] = xarModVars::get('comments', 'sortby');
        /*}*/
    }

    if (!isset($settings['depth']) || $settings['depth'] > (_COM_MAX_DEPTH - 1)) {
        $settings['depth'] = (_COM_MAX_DEPTH - 1);
    }

    if (empty($settings['render'])) {
        $settings['render'] = _COM_VIEW_THREADED;
    }
    if (empty($settings['order'])) {
        $settings['order'] = _COM_SORT_ASC;
    }
    if (empty($settings['sortby'])) {
        $settings['sortby'] = _COM_SORTBY_THREAD;
    }

    return $settings;
}
