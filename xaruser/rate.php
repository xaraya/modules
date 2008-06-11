<?php
/**
 * Ratings Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ratings Module
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * @return bool true
 */
function ratings_user_rate($args)
{
    // Get parameters
    if (!xarVarFetch('modname',   'isset', $modname,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('itemtype',  'isset', $itemtype,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('objectid',  'isset', $objectid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('returnurl', 'isset', $returnurl,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('rating',    'isset', $rating,     NULL, XARVAR_DONT_SET)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    $newrating = xarModAPIFunc('ratings',
                              'user',
                              'rate',
                              array('modname'    => $modname,
                                    'itemtype'   => $itemtype,
                                    'objectid'   => $objectid,
                                    'rating'     => $rating));

    if (isset($newrating)) {
        // Success
//            xarSessionSetVar('ratings_statusmsg', xarML('Thank you for rating this item.',
//                    'ratings'));
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>
