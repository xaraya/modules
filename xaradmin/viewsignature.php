<?php
/**
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
 * View a list of Newsletter owners
 *
 * @public
 * @author Richard Cave
 * @param int 'startnum' starting number to display
 * @return array $data
 */
function newsletter_admin_viewsignature($args)
{
    // Extract args
    extract ($args);

    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;

    // Get the admin menu
    $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Get the user id
    $uid = xarSessionGetVar('uid');

    // Only retrieve the owner
    $owner = xarModAPIFunc('newsletter',
                            'user',
                            'getowner',
                            array('id' => $uid));

    // Check for exceptions
    if (!isset($owner) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    $owner['edittitle'] = xarML('Edit');

    if(xarSecurityCheck('EditNewsletter', 0)) {
        $owner['editurl'] = xarModURL('newsletter',
                                      'admin',
                                      'modifyowner',
                                       array('id' => $owner['uid']));
    } else {
        $owner['editurl'] = '';
    }

    // Add the array of owner to the template variables
    $data['owner'] = $owner;

    // Return the template variables defined in this function
    return $data;
}

?>
