<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Select or add a new publication
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_selectpublication()
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get the admin menu
    // $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Choose the publication or create a new publication
    // $data['submitbutton'] = xarVarPrepForDisplay(xarML('Next'));

    // Get the list of publications
    $data['publications'] = xarModAPIFunc('newsletter',
                                          'user',
                                          'get',
                                          array('phase' => 'publication',
                                                'sortby' => 'title'));

    // Check for exceptions
    if (!isset($data['publications']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    // Return the template variables defined in this function
    return $data;

}

?>
