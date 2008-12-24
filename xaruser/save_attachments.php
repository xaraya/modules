<?php
/**
 * Save attachments
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 * Save attachments
 * @return bool true
 */
function uploads_user_save_attachments($args)
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

    xarResponseRedirect($returnurl);

    return true;
}

?>