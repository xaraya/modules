<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
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
        $depth = xarModGetVar('comments','depth');
    }

    if (empty($render)) {
        $render = xarModGetVar('comments','render');
    }

    if (empty($order)) {
        $order = xarModGetVar('comments','order');
    }

    if (empty($sortby)) {
        $sortby = xarModGetVar('comments','sortby');
    }

    if (xarUserIsLoggedIn()) {
            // Grab user's depth setting.
            xarModSetUserVar('comments','depth',$depth);
            xarModSetUserVar('comments','render',$render);
            xarModSetUserVar('comments','sortby',$sortby);
            xarModSetUserVar('comments','order',$order);
    }

    return true;

}

?>