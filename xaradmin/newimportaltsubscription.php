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
 * Import alternative subscriptions
 *
 * @public
 * @author Richard Cave
 * @param string func
 * @return array $data
 */
function newsletter_admin_newimportaltsubscription()
{
    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;
    // Get the admin edit menu
    $data['menu'] = xarModApiFunc('newsletter', 'admin', 'subscriptionmenu');

    // Options label
    // $data['importbutton'] = xarVarPrepForDisplay(xarML('Import Subscriptions'));

    // Set startnum to display all publications
    $startnum = 1;

    // The user API function is called.
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('newsletter',
                                                                  'itemsperpage'),
                                        'phase' => 'publication',
                                        'sortby' => 'title'));

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
