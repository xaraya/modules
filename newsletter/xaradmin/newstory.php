<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://xavier.schwabfoundation.org
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Add a new Newsletter story
 *
 * @public
 * @author Richard Cave
 * @param 'publicationId' the publication id of the story (0 = no publication)
 * @returns array
 * @return $data
 */
function newsletter_admin_newstory()
{
    // Security check
    if(!xarSecurityCheck('AddNewsletter')) return;

    // Get input parameters
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('ownerId', 'int:0:', $ownerId, 0)) return;
    if (!xarVarFetch('issueId', 'int:0:', $issueId, 0)) return;

    // Get the admin menu
    $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Set template strings
    $data['addlabel'] = xarVarPrepForDisplay(xarML('Finished'));
    $data['nextlabel'] = xarVarPrepForDisplay(xarML('Add Another Story'));

    // Get the list of publications
    $data['publications'] = xarModAPIFunc('newsletter',
                                          'user',
                                          'get',
                                           array('phase' => 'publication',
                                                 'sortby' => 'title'));
    
    // Check for exceptions
    if (!isset($data['publications']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)  {
        return; // throw back
    }
    
    // Check if we have an ownerid
    if (!$ownerId) {
        // Get current user
        $data['loggeduser'] = xarModAPIFunc('newsletter',
                                            'user',
                                            'getloggeduser');
                                            
        $ownerId = $data['loggeduser']['uid'];
    }                                   

    // Set owner id
    $data['ownerId'] = $ownerId;

    // Get the list of owners
    $data['owners'] = xarModAPIFunc('newsletter',
                                    'user',
                                    'get',
                                    array('phase' => 'owner'));

    if (empty($data['owners'])) {
        $msg = xarML('You must set an newsletter owner before creating an story.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get categories
    $data['number_of_categories'] = xarModGetVar('newsletter', 'number_of_categories');
    $data['categories'] = array();

    // If we have an issueId, then grab the publication for that issue
    if (!$publicationId && $issueId) {
        // Get issue
        $issue = xarModAPIFunc('newsletter',
                               'user',
                               'getissue',
                               array('id' => $issueId));

        // Check for exceptions
        if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }

        // Set publicationId
        $publicationId = $issue['pid'];
    }

    // Try to find a publication for an owner
    if (!$publicationId && $ownerId) {
        // See if a publication is owned by the current user
        foreach ($data['publications'] as $pub) {
            if ($pub['ownerId'] == $ownerId) {
                // Grab first publication that we find
                $publicationId = $pub['id'];
                break;
            }
        }
    }


    if ($publicationId) {
        // Get publication
        $pubItem = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $publicationId));

        // Check for exceptions
        if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION)  {
            return; // throw back
        }

        // Set publication link expiration
        $data['linkExpires'] = $pubItem['linkExpiration'];

        // Only show categories for publication
        $categories = xarModAPIFunc('newsletter',
                                     'user',
                                     'getchildcategories',
                                     array('parentcid' => $pubItem['cid'],
                                           'numcats' => $data['number_of_categories']));
        
        if ($categories) {
            $data['categories'] = $categories;
        }

        // Set publication title
        $data['publication_title'] = $pubItem['title'];

    } else {
        // No publication - so grab them all
        $mastercid = xarModGetVar('newsletter', 'mastercid');

        // Get the child categories below the master category
        $categories = xarModAPIFunc('newsletter',
                                    'user',
                                    'getchildcategories',
                                    array('parentcid' => $mastercid,
                                          'numcats' => $data['number_of_categories']));

        // Check for categories to display
        if (empty($categories)) {
            $msg = xarML('You must set an newsletter category for this publication.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
            return;
        }

        // Get the grandchild categories below the master category
        foreach ($categories as $category) {
            $grandchildren = xarModAPIFunc('newsletter',
                                           'admin',
                                           'appendchildcategories',
                                           array('parentcid' => $category['cid'],
                                                 'numcats' => $data['number_of_categories']));
        
            // Merge the category arrays
            $data['categories'] = array_merge($data['categories'], $grandchildren);
        }

        // Set publication title
        $data['publication_title'] = '';
    }

    // Set publication
    $data['publicationId'] = $publicationId;

    // Set issue
    $data['issueId'] = $issueId;

    // Default story date to today
    $now = time();
    $data['storyDate'] = array();
    $data['storyDate']['mon'] = strftime('%m',$now);
    $data['storyDate']['day'] = strftime('%d',$now);
    $data['storyDate']['year'] = strftime('%Y',$now);

    // Get the list of commentary sources
    $commentarySource = xarModGetVar('newsletter', 'commentarysource');
    if (!empty($commentarySource)) {
        if (!is_array($commentarySource = @unserialize($commentarySource))) {
            $commentarySource = array();
        }
    } else {
        $commentarySource = array();
    }

    // Check if publication is in commentary source array
    if (array_key_exists($publicationId, $commentarySource)) {
        $data['commentarySource'] = $commentarySource[$publicationId];
    } else {
        $data['commentarySource'] = array();
    }

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}

?>
