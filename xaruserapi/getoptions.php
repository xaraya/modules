<?php

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
    if (!xarVarFetch('depth', 'int', $depth, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('render', 'str', $render, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'int', $order, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'int', $sortby, 0, XARVAR_NOT_REQUIRED)) return;

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
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['depth'] = xarModGetUserVar('comments','depth');
        } else {
            $settings['depth'] = xarModGetVar('comments','depth');
        }
    }

    if (isset($render) && !empty($render)) {
        $settings['render'] = $render;
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['render'] = xarModGetUserVar('comments','render');
        } else {
            $settings['render'] = xarModGetVar('comments','render');
        }
    }

    if (isset($order) && !empty($order)) {
        $settings['order'] = $order;
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['order'] = xarModGetUserVar('comments','order');
        } else {
            $settings['order'] = xarModGetVar('comments','order');
        }
    }

    if (isset($sortby) && !empty($sortby)) {
        $settings['sortby'] = $sortby;
    } else {
        if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            $settings['sortby'] = xarModGetUserVar('comments','sortby');
        } else {
            $settings['sortby'] = xarModGetVar('comments','sortby');
        }
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

?>
