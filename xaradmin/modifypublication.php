<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Modify an Newsletter publication
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the publication to be modified
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifypublication() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;

    // The user API function is called
    $publication = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $id));

    // Check for exceptions
    if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get the list of owners
    $publication['owners'] = xarModAPIFunc('newsletter',
                                           'user',
                                           'get',
                                           array('phase' => 'owner'));

    // Get categories
    $publication['number_of_categories'] = xarModGetVar('newsletter', 'number_of_categories');
    $mastercid = xarModGetVar('newsletter', 'mastercid');
    $publication['categories'] = xarModAPIFunc('newsletter',
                                               'user',
                                               'getchildcategories',
                                               array('parentcid' => $mastercid,
                                                     'numcats' => $publication['number_of_categories']));

    // Get the list of disclaimers
    $publication['disclaimers'] = xarModAPIFunc('newsletter',
                                                'user',
                                                'get',
                                                array('phase' => 'disclaimer'));
                                                
    // Check for exceptions
    if (!isset($publication['disclaimers']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back
                                                
    // Get the publication disclaimer
    $disclaimer = xarModAPIFunc('newsletter',
                                'user',
                                'getdisclaimer',
                                 array('id' => $publication['disclaimerId']));
                                 
    // Check for exceptions
    if (!isset($disclaimer) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back     

    // Get all categories for altcids drop down
    $altcategories = xarModAPIFunc('categories',
                                   'user',
                                   'getcat');
                                                                                              
    // Check for exceptions
    if (!isset($altcategories) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return;
    }       
    
    // Loop through altcategories and check those that are part of publication
    for ($idx = 0; $idx < count($altcategories); $idx++) {
        if (in_array($altcategories[$idx]['cid'], $publication['altcids'])) {
            $altcategories[$idx]['selected'] = 1;
        } else {
            $altcategories[$idx]['selected'] = 0;
        }
    }      
                              
    // Set hook variables
    $publication['module'] = 'newsletter';
    $hooks = xarModCallHooks('publication','modify',$id,$publication);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the user menu
    $menu = xarModAPIFunc('newsletter', 'user', 'menu');

    // Set the template variables defined in this function
    $templateVarArray = array('authid' => xarSecGenAuthKey(),
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Publication')),
        'hooks' => $hooks,
        'menu' => $menu,
        'publication' => $publication,
        'disclaimer' => $disclaimer,
        'altcategories' => $altcategories);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
