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
 * Get the contents of a single issue
 * 
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['issueId'] issue id
 * @returns array
 * @return $issue
 */
function newsletter_userapi_getissuefordisplay($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($issueId) || !is_numeric($issueId)) {
        $invalid[] = 'issue id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getissuefordisplay', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // initialize array
    $issue = array();

    // The API function is called.  The arguments to the function are passed in
    $issue = xarModAPIFunc('newsletter',
                           'user',
                           'getissue',
                           array('id' => $issueId));

    // Check for exceptions
    if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check if anything was returned
    if (!$issue) {
        $msg = xarML('Invalid issue.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; // throw back
    }

    // Change date formats from UNIX timestamp to something readable
    if ($issue['datePublished']['timestamp'] == 0) {
        $issue['datePublished']['mon'] = "";
        $issue['datePublished']['day'] = "";
        $issue['datePublished']['year'] = "";
    } else {
        $issue['datePublished']['mon'] = date('m', $issue['datePublished']['timestamp']);
        $issue['datePublished']['day'] = date('d', $issue['datePublished']['timestamp']);
        $issue['datePublished']['year'] = date('Y', $issue['datePublished']['timestamp']);
    }

    // Get owner name
    $userData = xarModAPIFunc('roles',
                              'user',
                              'get',
                               array('uid' => $issue['ownerId']));

    if ($userData) {
        $issue['ownerName'] = $userData['name'];
        
        // Get the owner signature
        $owner = xarModAPIFunc('newsletter',
                               'user',
                               'getowner',
                               array('id' => $issue['ownerId']));

        if ($owner) {
            $issue['ownerSignature'] = $owner['signature'];
        } else {
            $issue['ownerSignature'] = "";
        }
    } else {
        // User does not exist in xar_roles table
        $issue['ownerName'] = "Unknown User";
        $issue['ownerSignature'] = "";
    }

    // Get topics for issue - these are the stories
    // associated with issue
    $topics = xarModAPIFunc('newsletter',
                            'user',
                            'get',
                            array('issueId' => $issueId,
                                  'phase' => 'topic'));

    // Check for exceptions
    if (!isset($topics) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get ready for headlines
    $headlines = array();

    // Get the stories...
    $now = time();
    $stories = array();
    for ($idx=0; $idx < count($topics); $idx++) {
        $stories[$idx] = xarModAPIFunc('newsletter',
                                       'user',
                                       'getstory',
                                       array('id' => $topics[$idx]['storyId']));

        // Check for exceptions
        if (!isset($stories[$idx]) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back
        
        // Make sure a story was returned
        if (!empty($stories[$idx])) {
            // Check to see if link has expired
            $linkExpiration = $stories[$idx]['linkExpiration']; 
            $storyDate = $stories[$idx]['storyDate'];
            $stories[$idx]['linkExpired'] = false;
            if ($storyDate['timestamp'] != 0 && $linkExpiration != 0) {
                if ($now - ($linkExpiration * 86400) > $storyDate['timestamp']) {
                    // Link expired - so don't display the link
                    $stories[$idx]['linkExpired'] = true;
                }
            }

            // Check that we have a real category id
            if ($stories[$idx]['cid'] != 0) {

                // Get category
                $category = xarModAPIFunc('categories',
                                          'user',
                                          'getcatinfo', // may need to change to getcat
                                          Array('cid' => $stories[$idx]['cid']));

                // Check for exceptions
                if (!isset($category) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                    return; // throw back

                // Set headlines
                $found = false;
                for ($cdx=0; $cdx < count($headlines); $cdx++) {
                    if ($headlines[$cdx]['category'] == $category['name']) {
                        // Add story to array
                        $headlines[$cdx]['stories'][] = $stories[$idx];
                        $found = true;
                        break;
                    }
                }

                // New headline so insert into array
                if (!$found) {
                    $storiesArray = array();
                    $storiesArray[] = $stories[$idx];
                    $headlines[] = array('category' => $category['name'],
                                         'stories' => $storiesArray);
                }
            } else {
                // Since there is no category, just add the story
                // the array
                $storiesArray = array();
                $storiesArray[] = $stories[$idx];
                $headlines[] = array('category' => '',
                                     'stories' => $storiesArray);
            }
        }
    }

    // Assign headlines 
    $issue['headlines'] = $headlines; 

    return $issue;
}

?>
