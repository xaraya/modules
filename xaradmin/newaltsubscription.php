<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */

/**
 * Add a single alternative subscription for a publication
 *
 * @public
 * @author Richard Cave
 * @param string func What function are we in currently. Used by the menu.
 * @return array $data
 */
function newsletter_admin_newaltsubscription()
{
    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;

    // Get the admin subscription menu
    $data['menu'] = xarModApiFunc('newsletter', 'admin', 'subscriptionmenu');

    // Options label
    $data['publishername'] = xarModGetVar('newsletter', 'publishername');

    // The user API function is called to get all publications
    // We set a very high number of items to make sure we get all.
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('phase' => 'publication',
                                        'sortby' => 'title',
                                        'numitems' => 200));

    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Add the array of items to the template variables
    $data['publications'] = $publications;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}
?>
