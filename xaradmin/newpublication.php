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
 * Add a new Newsletter publication
 *
 * @public
 * @author Richard Cave
 * @return array $data
 */
function newsletter_admin_newpublication()
{
    // Security check
    if(!xarSecurityCheck('AddNewsletter')) return;
    $data = array();
    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;

    // Get the admin edit menu
    $data['menu'] = xarModAPIFunc('newsletter', 'admin', 'configmenu');
    // Get current user
    $data['loggeduser'] = xarModAPIFunc('newsletter',
                                        'user',
                                        'getloggeduser');

    // Get the list of owners
    $data['owners'] = xarModAPIFunc('newsletter',
                                    'user',
                                    'get',
                                     array('phase' => 'owner'));

    if (empty($data['owners'])) {
        $msg = xarML('You must add an Newsletter user before creating a publication.  Please add a user through Modify Users in the Newsletter administration configuration.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get newsletter categories
    $data['number_of_categories'] = xarModGetVar('newsletter', 'number_of_categories');
    $mastercid = xarModGetVar('newsletter', 'mastercid');
    $categories = xarModAPIFunc('newsletter',
                                'user',
                                'getchildcategories',
                                array('parentcid' => $mastercid,
                                      'numcats' => $data['number_of_categories']));

    // Check for categories to display
    if (empty($categories)) {
        $msg = xarML('You must create categories under the Newsletter category before creating a publication.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $data['categories'] = $categories;

    // Get all categories for altcids drop down
    $data['altcategories'] = xarModAPIFunc('categories',
                                           'user',
                                           'getcat');

    // Check for exceptions
    if (!isset($data['altcategories']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return;
    }

    // Get the list of disclaimers
    $data['disclaimers'] = xarModAPIFunc('newsletter',
                               'user',
                               'get',
                                array('phase' => 'disclaimer'));

    // Check for exceptions
    if (!isset($data['disclaimers']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Get the publication template
    $data['templateHTML'] = xarModGetVar('newsletter', 'templateHTML');
    $data['templateText'] = xarModGetVar('newsletter', 'templateText');

    // Set private flag to 0
    $data['private'] = 0;

    // Get default link expiration
    $data['linkExpiration'] = xarModGetVar('newsletter', 'linkexpiration');
    $data['linkRegistration'] = xarModGetVar('newsletter', 'linkregistration');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}

?>
