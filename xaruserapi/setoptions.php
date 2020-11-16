<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Set a user's viewing options
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_setoptions($args)
{
    extract($args);

    if (isset($depth)) {
        if ($depth == 0) {
            $depth = 1;
        }
        if ($depth > (_COM_MAX_DEPTH - 1)) {
            $depth = (_COM_MAX_DEPTH - 1);
        }
    } else {
        $depth = xarModVars::get('comments', 'depth');
    }

    if (empty($render)) {
        $render = xarModVars::get('comments', 'render');
    }

    if (empty($order)) {
        $order = xarModVars::get('comments', 'order');
    }

    if (empty($sortby)) {
        $sortby = xarModVars::get('comments', 'sortby');
    }

    if (xarUser::isLoggedIn()) {
        // Grab user's depth setting.
        xarModUserVars::set('comments', 'depth', $depth);
        xarModUserVars::set('comments', 'render', $render);
        xarModUserVars::set('comments', 'sortby', $sortby);
        xarModUserVars::set('comments', 'order', $order);
    }

    return true;
}
