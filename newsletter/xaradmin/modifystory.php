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
 * Modify an Newsletter story
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the story to be modified
 * @param 'publicationId' publication id of the issue the story is in
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifystory() 
{
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;

    // Get input parameters
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;

    // The user API function is called
    $story = xarModAPIFunc('newsletter',
                           'user',
                           'getstory',
                           array('id' => $id));

    // Check for exceptions
    if (!isset($story) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    $story['publications'] = xarModAPIFunc('newsletter',
                                           'user',
                                           'get',
                                            array('phase' => 'publication',
                                                  'sortby' => 'title'));
    
    // Check for exceptions
    if (!isset($story['publications']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Find the publication based on story category
    if ($publicationId != 0) {
        // Get the chosen publication
        $pubItem = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $publicationId));

        // Check for exceptions
        if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back
    } else {
        // Check if the story has a publication already
        if ($story['pid'] != 0) {
            $publicationId = $story['pid'];

            // Get the chosen publication
            $pubItem = xarModAPIFunc('newsletter',
                                     'user',
                                     'getpublication',
                                     array('id' => $publicationId));

            // Check for exceptions
            if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return; // throw back

        } elseif ($story['cid'] != 0) {
            // Get the parent category of the story
            $storyCategory =  xarModAPIFunc('categories',
                                            'user',
                                            'getcatinfo',
                                             Array('cid' => $story['cid']));

            // Get parent publication
            $pubItem =  xarModAPIFunc('categories',
                                      'user',
                                      'getcatinfo',
                                      Array('cid' => $storyCategory['parent']));

            // Set publication
            foreach ($story['publications'] as $storyPub) {
                if ($storyPub['cid'] == $pubItem['cid']) {
                    $publicationId = $storyPub['id'];
                    break;
                }
            }
        } else {
            // No category found so set to top level
            $pubItem['cid'] = xarModGetVar('newsletter', 'mastercid');
            $publicationId = 0;
        }
    }

    // Set some more variables
    $story['publicationId'] = $publicationId;
    $story['categories'] = array();
    $story['number_of_categories'] = xarModGetVar('newsletter', 'number_of_categories');

    if ($publicationId != 0) {

        // Only show categories for publication
        $categories = xarModAPIFunc('newsletter',
                                     'user',
                                     'getchildcategories',
                                     array('parentcid' => $pubItem['cid'],
                                           'numcats' => $story['number_of_categories']));
        
        if ($categories)
            $story['categories'] = $categories;
    
    } else {
        // Get the child categories below the master category
        $categories = xarModAPIFunc('newsletter',
                                     'user',
                                     'getchildcategories',
                                     array('parentcid' => $pubItem['cid'],
                                           'numcats' => $story['number_of_categories']));

        // Get the grandchild categories below the master category
        foreach ($categories as $category) {
            $grandchildren = xarModAPIFunc('newsletter',
                                     'user',
                                     'appendchildcategories',
                                     array('parentcid' => $category['cid'],
                                           'numcats' => $story['number_of_categories']));

            // Merge the category arrays
            $story['categories'] = array_merge($story['categories'], $grandchildren);
        }
    }

    // Get the list of owners
    $story['owners'] = xarModAPIFunc('newsletter',
                                       'user',
                                       'get',
                                       array('phase' => 'owner'));

    // Check for exceptions
    if (!isset($story['owners']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get the list of commentary sources from module var
    $commsource = xarModGetVar('newsletter', 'commentarysource');
    if (!empty($commsource)) {
        if (!is_array($commsource = @unserialize($commsource))) {
            $commsource = array();
        }
    } else {
        $commsource = array();
    }
        
    // Get the list of publications
    // Set hook variables
    $story['module'] = 'newsletter';
    $hooks = xarModCallHooks('story','modify',$id,$story);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Set template array
    $templateVarArray = array(
        'authid' => xarSecGenAuthKey(),
        'updatebutton' => xarVarPrepForDisplay(xarML('Update Story')),
        'menu' => $menu,
        'hooks' => $hooks,
        'story' => $story);

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
