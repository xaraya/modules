<?php
/**
 * Delete a note
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete a note
 *
 * @param $rnid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_deletenote()
{
    // Get parameters
    if (!xarVar::fetch('rnid', 'id', $rnid)) {
        return;
    }
    if (!xarVar::fetch('obid', 'str:1:', $obid, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirmation', 'str:1:', $confirmation, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    
    if (!empty($obid)) {
        $rnid = $obid;
    }

    // The user API function is called.
    $data = xarMod::apiFunc(
        'release',
        'user',
        'getnote',
        array('rnid' => $rnid)
    );

    if ($data == false) {
        return;
    }

    // Security Check
    if (!xarSecurity::check('ManageRelease')) {
        return;
    }

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    if (!xarMod::apiFunc(
        'release',
        'admin',
        'deletenote',
        array('rnid' => $rnid)
    )) {
        return;
    }

    // Redirect
    xarController::redirect(xarController::URL('release', 'admin', 'viewnotes'));

    // Return
    return true;
}
