<?php

/**
 * File: $Id$
 *
 * Delete a page type
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_deletetype($args)
{
    extract($args);

    if (!xarVar::fetch('ptid', 'id', $ptid)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Security check
    if (!xarSecurity::check('AdminXarpagesPagetype', 1)) {
        return false;
    }

    // Get page type information
    $type = xarMod::apiFunc(
        'xarpages',
        'user',
        'gettype',
        ['ptid' => $ptid]
    );

    if (empty($type)) {
        $msg = xarML('The page type "#(1)" to be deleted does not exist', $ptid);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check for confirmation
    if (empty($confirm)) {
        $data = ['type' => $type];
        $data['authkey'] = xarSec::genAuthKey();

        // Get a count of pages that will also be deleted.
        $data['count'] = xarMod::apiFunc(
            'xarpages',
            'user',
            'getpages',
            ['count' => true, 'itemtype' => $type['ptid']]
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
        'deletetype',
        ['ptid' => $ptid]
    )
    ) {
        return;
    }

    xarController::redirect(xarController::URL('xarpages', 'admin', 'viewtypes'));

    return true;
}
