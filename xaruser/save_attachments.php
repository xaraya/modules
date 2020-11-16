<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * Save attachments
 * @return bool true
 */
function uploads_user_save_attachments($args)
{
    // Get parameters
    if (!xarVarFetch('modname', 'isset', $modname, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('itemtype', 'isset', $itemtype, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('objectid', 'isset', $objectid, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('returnurl', 'isset', $returnurl, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('rating', 'isset', $rating, null, XARVAR_DONT_SET)) {
        return;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        return;
    }

    // Pass to API
    $newrating = xarModAPIFunc(
        'ratings',
        'user',
        'rate',
        array('modname'    => $modname,
                                    'itemtype'   => $itemtype,
                                    'objectid'   => $objectid,
                                    'rating'     => $rating)
    );

    xarController::redirect($returnurl);

    return true;
}
