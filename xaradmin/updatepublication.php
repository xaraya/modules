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
 * Update a publication
 * @public
 * @param 'ownerId' the id of the publication owner (uid in roles)
 * @param 'categoryId' the category id of the publiction
 * @param 'altcids' array of alternate category ids for the publication
 * @param 'title' the title of the publication
 * @param 'disclaimerId' disclaimer id for the publication
 * @param 'editdisclaimer' new or edit of the disclaimer for the publication
 * @param 'altcids' array of alternate category ids for the publication
 * @param 'templateHTML' the HTML template for the publication
 * @param 'templateText' the text template for the publication
 * @param 'logo' the logo of the publication
 * @param 'linkExpiration' default number of days before a story link expires
 * @param 'linkRegistration' default text for link registration
 * @param 'introduction' introduction of the publication
 * @param 'description' description of the publication (used on subscription page)
 * @param 'private' publication is open for subscription or private
 * @param 'subject' email subject (title) for an issue
 * @param 'fromname' publication email from name (default = owner name)
 * @param 'fromemail' publication email from address (default = owner email)
 * @author Richard Cave
 * @returns bool
 * @return true on success, false on failure
 */
function newsletter_admin_updatepublication()
{
    // Confirm authorization code
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for updating #(1) item #(2) in function #(3)',
                    'Newsletter', xarVarPrepForDisplay($id), 'newsletter_admin_updatepublication');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('id', 'id', $id)) return;

    if (!xarVarFetch('ownerId', 'id', $ownerId)) {
        xarErrorFree();
        $msg = xarML('You must select an owner name.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    if (!xarVarFetch('categoryId', 'id', $categoryId, 0)) return;

    if (!xarVarFetch('title', 'str:1:', $title)) {
        xarErrorFree();
        $msg = xarML('You must enter a publication title');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $defaultValue = xarModGetVar('newsletter', 'templateHTML');
    if (!xarVarFetch('templateHTML', 'str:1:', $templateHTML, $defaultValue)) return;

    $defaultValue = xarModGetVar('newsletter', 'templateText');
    if (!xarVarFetch('templateText', 'str:1:', $templateText, $defaultValue)) return;

    $defaultValue = xarModGetVar('newsletter', 'linkexpiration');
    if (!xarVarFetch('linkExpiration', 'int:0:', $linkExpiration, $defaultValue)) return;

    $defaultValue = xarModGetVar('newsletter', 'linkregistration');
    if (!xarVarFetch('linkRegistration', 'str:1:', $linkRegistration, $defaultValue)) return;

    if (!xarVarFetch('logo', 'str:1:', $logo, '')) return;
    if (!xarVarFetch('description', 'str:1:', $description, '')) return;
    if (!xarVarFetch('disclaimerId', 'id', $disclaimerId, 0)) return;
    if (!xarVarFetch('editdisclaimer', 'str:1:', $editdisclaimer, '')) return;
    if (!xarVarFetch('introduction', 'str:1:', $introduction, '')) return;
    if (!xarVarFetch('altcids', 'array:1:', $altcids, array())) return;
    if (!xarVarFetch('private', 'int:0:1:', $private, 0)) return;
    if (!xarVarFetch('subject', 'id', $subject, 0)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, '')) return;
    if (!xarVarFetch('fromemail', 'str:1:', $fromemail, '')) return;

    // If the fromname or fromemail fields are empty, then retrieve the information
    // from the publication owner
    if (empty($fromname) || empty($fromemail)) {
        // Get owner information
        $role = xarModAPIFunc('roles',
                              'user',
                              'get',
                               array('uid' => $ownerId));
        // Check return value
        if (!isset($role) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back

        // Set name and/or email
        if (empty($fromname)) {
            $fromname = $role['name'];
        }
        if (empty($fromemail)) {
            $fromemail = $role['email'];
        }
    }

    // Update the disclaimer
    if (!empty($editdisclaimer)) {
        // Check if disclaimer is empty and add
        if ($disclaimerId == 0) {
            // Add disclaimer
            $disclaimerId = xarModAPIFunc('newsletter',
                                          'admin',
                                          'createdisclaimer',
                                          array('title' => $title,
                                                'disclaimer' => $editdisclaimer));

            // Check return value
            if (!isset($disclaimerId) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return; // throw back
        } else {
            // Get the current publication
            $publication = xarModAPIFunc('newsletter',
                                         'user',
                                         'getpublication',
                                         array('id' => $id));

            // Check for exceptions
            if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return; // throw back

            // Only change the disclaimer if it's the same as the publication
            if ($disclaimerId == $publication['disclaimerId']) {
                // Get the disclaimer
                $disclaimer = xarModAPIFunc('newsletter',
                                            'user',
                                            'getdisclaimer',
                                             array('id' => $disclaimerId));
                                             
                // Check for exceptions
                if (!isset($disclaimer) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
                    return; // throw back     

                // Check if content of disclaimer is the same
                if (strcmp($editdisclaimer, $disclaimer['disclaimer']) != 0) {
                    // Update the disclaimer
                    if(!xarModAPIFunc('newsletter',
                                      'admin',
                                      'updatedisclaimer',
                                      array('id' => $disclaimer['id'],
                                            'title' => $disclaimer['title'],
                                            'disclaimer' => $editdisclaimer))) {
                        return; // throw back
                    }
                }
            }
        }
    }

    // Call API function
    if(!xarModAPIFunc('newsletter',
                      'admin',
                      'updatepublication',
                      array('id' => $id,
                            'ownerId' => $ownerId,
                            'categoryId' => $categoryId,
                            'altcids' => $altcids,
                            'title' => $title,
                            'templateHTML' => $templateHTML,
                            'templateText' => $templateText,
                            'logo' => $logo,
                            'linkExpiration' => $linkExpiration,
                            'linkRegistration' => $linkRegistration,
                            'description' => $description,
                            'disclaimerId' => $disclaimerId,
                            'introduction' => $introduction,
                            'private' => $private,
                            'subject' => $subject,
                            'fromname' => $fromname,
                            'fromemail' => $fromemail))) {
        return; // throw back
    }

    xarSessionSetVar('statusmsg', xarML('Newsletter Story Update'));

    // Redirect
    xarResponseRedirect(xarModURL('newsletter', 'admin', 'viewpublication'));

    // Return
    return true;
}

?>
