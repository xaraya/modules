<?php

/**
 * File: $Id$
 *
 * Implement comments API backend
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

// FIXME: simplify all this header, package, receipt stuff

/**
 * include module constants and definitions
 *
 */

include_once('modules/comments/xarincludes/defines.php');

function comments_user_formhooks()
{

    $hooks = array();
    $hooks['formaction']              = xarModCallHooks('item', 'formaction', '', array(), 'comments');
    $hooks['formdisplay']             = xarModCallHooks('item', 'formdisplay', '', array(), 'comments');

    if (empty($hooks['formaction'])){
        $hooks['formaction'] = '';
    } elseif (is_array($hooks['formaction'])) {
        $hooks['formaction'] = join('',$hooks['formaction']);
    }

    if (empty($hooks['formdisplay'])){
        $hooks['formdisplay'] = '';
    } elseif (is_array($hooks['formdisplay'])) {
        $hooks['formdisplay'] = join('',$hooks['formdisplay']);
    }

    return $hooks;
}


/**
 * Collapse a comment branch and store the parent where
 * the collapsing begins in a uservar
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_collapse( ) {

    $headers = xarRequestGetVar('headers');
    $package = xarRequestGetVar('package');
    $receipt = xarRequestGetVar('receipt');
    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    if (xarUserIsLoggedIn()) {

        $branches = unserialize(xarModGetUserVar('comments','CollapsedBranches'));

        if (!array_key_exists($header['cid'], $branches)) {
            $branches[$header['cid']] = $header['cid'];
            xarModSetUserVar('comments','CollapsedBranches',serialize($branches));
        }
    }

    $args['header[modid]']               = $header['modid'];
    $args['header[itemtype]']            = $header['itemtype'];
    $args['header[objectid]']            = $header['objectid'];

    if (isset($header['selected_cid'])) {
        $args['header[selected_cid]']        = $header['selected_cid'];
    }

    if (isset($header['branchout'])) {
        $args['header[branchout]']           = $header['branchout'];
        $args['header[cid]']                 = $header['cid'];
    }

    $args['receipt[returnurl][encoded]'] = $receipt['returnurl']['encoded'];
    $args['receipt[returnurl][decoded]'] = $receipt['returnurl']['decoded'];

    $url = $args['receipt[returnurl][decoded]'];

    foreach ($args as $k=>$v) {
        $url .= "&amp;$k=$v";
    }

    xarResponseRedirect($url);
}


/**
 * Expand a previously collapsed branch
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_expand( ) {

    $headers = xarRequestGetVar('headers');
    $package = xarRequestGetVar('package');
    $receipt = xarRequestGetVar('receipt');
    $package['settings'] = xarModAPIFunc('comments','user','getoptions');

    if (!isset($header['itemtype'])) {
        $header['itemtype'] = 0;
    }

    if (xarUserIsLoggedIn()) {

        $branches = unserialize(xarModGetUserVar('comments','CollapsedBranches'));

        if (array_key_exists($header['cid'], $branches)) {
            unset($branches[$header['cid']]);
            xarModSetUserVar('comments','CollapsedBranches',serialize($branches));
        }
    }

    $args['header[modid]']               = $header['modid'];
    $args['header[itemtype]']            = $header['itemtype'];
    $args['header[objectid]']            = $header['objectid'];

    if (isset($header['selected_cid'])) {
        $args['header[selected_cid]']        = $header['selected_cid'];
    }

    if (isset($header['branchout'])) {
        $args['header[branchout]']           = $header['branchout'];
        $args['header[cid]']                 = $header['cid'];
    }

    $args['receipt[returnurl][encoded]'] = $receipt['returnurl']['encoded'];
    $args['receipt[returnurl][decoded]'] = $receipt['returnurl']['decoded'];

    $url = $args['receipt[returnurl][decoded]'];

    foreach ($args as $k=>$v) {
        $url .= "&amp;$k=$v";
    }

    xarResponseRedirect($url);
}

?>
