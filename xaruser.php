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
// FIXME: simplify all this header, package, receipt stuff

/**
 * include module constants and definitions
 *
 */

sys::import('modules.comments.xarincludes.defines');

/***
 *
 * These functions are being saved in the event that I actually deide to use them at
 * some point - currently however, this functionality is being done using css.
 *
 ***
 *

 **
 * Collapse a comment branch and store the parent where
 * the collapsing begins in a uservar
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 *
function comments_userapi_collapse( )
{

    $headers = xarRequestGetVar('headers');
    $package = xarRequestGetVar('package');
    $receipt = xarRequestGetVar('receipt');
    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    if (xarUserIsLoggedIn()) {

        $branches = unserialize(xarModGetUserVar('comments','CollapsedBranches'));

        if (!array_key_exists($header['id'], $branches)) {
            $branches[$header['id']] = $header['id'];
            xarModSetUserVar('comments','CollapsedBranches',serialize($branches));
        }
    }

    $args['header[modid]']               = $header['modid'];
    $args['header[itemtype]']            = $header['itemtype'];
    $args['header[objectid]']            = $header['objectid'];

    if (isset($header['selected_id'])) {
        $args['header[selected_id]']        = $header['selected_id'];
    }

    if (isset($header['branchout'])) {
        $args['header[branchout]']           = $header['branchout'];
        $args['header[id]']                 = $header['id'];
    }

    $args['receipt[returnurl][encoded]'] = $receipt['returnurl']['encoded'];
    $args['receipt[returnurl][decoded]'] = $receipt['returnurl']['decoded'];

    $url = $args['receipt[returnurl][decoded]'];

    foreach ($args as $k=>$v) {
        $url .= "&amp;$k=$v";
    }

    xarResponseRedirect($url);
}


 **
 * Expand a previously collapsed branch
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 *
function comments_userapi_expand( )
{

    $headers = xarRequestGetVar('headers');
    $package = xarRequestGetVar('package');
    $receipt = xarRequestGetVar('receipt');
    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    if (xarUserIsLoggedIn()) {

        $branches = unserialize(xarModGetUserVar('comments','CollapsedBranches'));

        if (array_key_exists($header['id'], $branches)) {
            unset($branches[$header['id']]);
            xarModSetUserVar('comments','CollapsedBranches',serialize($branches));
        }
    }

    $args['header[modid]']               = $header['modid'];
    $args['header[itemtype]']            = $header['itemtype'];
    $args['header[objectid]']            = $header['objectid'];

    if (isset($header['selected_id'])) {
        $args['header[selected_id]']        = $header['selected_id'];
    }

    if (isset($header['branchout'])) {
        $args['header[branchout]']           = $header['branchout'];
        $args['header[id]']                 = $header['id'];
    }

    $args['receipt[returnurl][encoded]'] = $receipt['returnurl']['encoded'];
    $args['receipt[returnurl][decoded]'] = $receipt['returnurl']['decoded'];

    $url = $args['receipt[returnurl][decoded]'];

    foreach ($args as $k=>$v) {
        $url .= "&amp;$k=$v";
    }

    xarResponseRedirect($url);
}

*/

?>