<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * @return bool true
 */
function ratings_user_rate($args)
{
    // Get parameters
    if (!xarVarFetch('modname', 'isset', $modname, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('itemtype', 'isset', $itemtype, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('itemid', 'isset', $itemid, null, XARVAR_DONT_SET)) {
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
    $newrating = xarMod::apiFunc(
        'ratings',
        'user',
        'rate',
        array('modname'    => $modname,
                                    'itemtype'   => $itemtype,
                                    'itemid'     => $itemid,
                                    'rating'     => $rating)
    );

    if (isset($newrating)) {
        // Success
        xarSession::setVar('ratings_statusmsg', xarML(
            'Thank you for rating this item.',
            'ratings'
        ));
    }

    xarController::redirect($returnurl);

    return true;
}
