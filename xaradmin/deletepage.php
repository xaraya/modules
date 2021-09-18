<?php

/**
 * File: $Id$
 *
 * Delete a page
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_deletepage($args)
{
    extract($args);

    if (!xarVar::fetch('pid', 'id', $pid)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('return_url', 'str:0:200', $return_url, '', xarVar::DONT_SET)) {
        return;
    }

    // Get page information
    $page = xarMod::apiFunc(
        'xarpages',
        'user',
        'getpage',
        ['pid' => $pid]
    );

    if (empty($page)) {
        $msg = xarML('The page #(1) to be deleted does not exist', $pid);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Security check
    if (!xarSecurity::check('DeleteXarpagesPage', 1, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
        return false;
    }

    // Check for confirmation
    if (empty($confirm)) {
        $data = ['page' => $page, 'return_url' => $return_url];
        $data['authkey'] = xarSec::genAuthKey();

        $data['count'] = xarMod::apiFunc(
            'xarpages',
            'user',
            'getpages',
            ['count' => true, 'left_range' => [$page['left']+1, $page['right']-1]]
        );

        // Return output
        return $data;
    }

    // Confirm Auth Key
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Pass to API
    if (!xarMod::apiFunc(
        'xarpages',
        'admin',
        'deletepage',
        ['pid' => $pid]
    )
    ) {
        return;
    }

    if (!empty($return_url)) {
        xarController::redirect($return_url);
    } else {
        xarController::redirect(xarController::URL('xarpages', 'admin', 'viewpages'));
    }

    return true;
}
