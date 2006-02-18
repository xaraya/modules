<?php
/*
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Subscribe to an Newsletter
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_user_newsubscription()
{
    // Security check
    if(!xarSecurityCheck('ReadNewsletter')) return;

    // Get the user menu
    //$data = xarModAPIFunc('newsletter', 'user', 'menu');

    // Specify some other variables used in the blocklayout template
    //$data['welcome'] = xarML('Subscribe to a Newsletter');

    // Verify that the user is logged in - the user has
    // to registered and in the roles table or else
    // subscription is not possible
    if (!xarUserIsLoggedIn()) {
        $data['loggedin'] = false;
    } else {
        $data['loggedin'] = true;

        // Get publisher name
        $data['publishername'] = xarModGetVar('newsletter', 'publishername');
       // $data['subscribebutton'] = xarVarPrepForDisplay(xarML('Subscribe'));

        // Get all the publications available
        $publications = xarModAPIFunc('newsletter',
                                      'user',
                                      'get',
                                      array('phase' => 'publication',
                                            'sortby' => 'title'));

        // Check for exceptions
        if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
            return; // throw back

        // Convert newlines in text to <br /> for display
        for ($idx = 0; $idx < count($publications); $idx++) {
            // may be I'm wrong here
            //$brtext = nl2br($publications[$idx]['description']);
            //$publications[$idx]['description'] = $brtext;
            $brtext = nl2br($publications[$idx]['introduction']);
            $publications[$idx]['introduction'] = $brtext;
        }

        $data['publications'] = $publications;
    }

    // Return the template variables defined in this function
    return $data;

}

?>
