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
    if (!xarVar::fetch('modname', 'isset', $modname, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'isset', $itemtype, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('objectid', 'isset', $objectid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'isset', $returnurl, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('rating', 'isset', $rating, null, xarVar::DONT_SET)) {
        return;
    }

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Pass to API
    $newrating = xarMod::apiFunc(
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
