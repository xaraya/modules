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
 * View a list of Newsletter stories
 *
 * @public
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @param 'sortby' sort stories by 'publicaiton', 'title, 'category', 'date' or 'owner'
 * @param 'owner' show only logged user stories (1=true, 0=false)
 * @param 'display' show 'published' or 'unpublished' or 'all' stories
 * @param 'issueId' get stories for a specific issue
 * @returns array
 * @return $data
 */
function newsletter_admin_viewstory($args)
{

    // Extract args
    extract ($args);

    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'date')) return;
    if (!xarVarFetch('owner', 'int:0:1', $owner, 1)) return;
    if (!xarVarFetch('display', 'str:1:', $display, 'unpublished')) return;
    if (!xarVarFetch('issueId', 'int:0:', $issueId, 0)) return;
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'editmenu');

    // Get the issue title
    $data['issue_title'] = '';
    if ($issueId) {
        $issue = xarModAPIFunc('newsletter',
                               'user',
                               'getissue',
                               array('id' => $issueId));

        // Check for exceptions
        if (!isset($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }
        
        $data['issue_title'] = $issue['title'];
    }

    // Set issue 
    $data['issueId'] = $issueId;

    // Prepare the array variable that will hold all items for display
    $data['stories'] = array();
    $data['startnum'] = $startnum;
    $data['sortby'] = $sortby;
    $data['owner'] = $owner;
    $data['display'] = $display;

    // Get current uid
    $userid = xarSessionGetVar('uid');

    // If issueId, then retrieve stories for that issue only
    if ($issueId) {
        // Get all the stories for a publication
        $stories = xarModAPIFunc('newsletter',
                                  'user',
                                  'getissuestories',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('newsletter',
                                                                   'itemsperpage'),
                                        'phase' => 'story',
                                        'sortby' => $sortby,
                                        'owner' => $owner,
                                        'display' => $display,
                                        'issueId' => $issueId));

        // Check for exceptions
        if (!isset($stories) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }
    } else {
        // Get all the stories for a publication
        $stories = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('newsletter',
                                                                   'itemsperpage'),
                                        'phase' => 'story',
                                        'sortby' => $sortby,
                                        'owner' => $owner,
                                        'display' => $display,
                                        'publicationId' => $publicationId));

        // Check for exceptions
        if (!isset($stories) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }
    }

    // Get category name and parent category name
    $data['showcategory'] = false;
    for ($idx = 0; $idx < count($stories); $idx++) {
            
        if ($stories[$idx]['cid'] != 0 ) {
            $category = xarModAPIFunc('categories',
                                      'user',
                                      'getcatinfo', // may need to change to getcat
                                      Array('cid' => $stories[$idx]['cid']));

            // Check for exceptions
            if (!isset($category) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return;

            // Get category parent name and prepend
            //$parent = xarModAPIFunc('categories',
            //                        'user',
            //                        'getcatinfo',
            //                        Array('cid' => $category['parent']));

            //if (!empty($parent)) {
            //    $name = $category['name'];
            //    $category['name'] = "[" . $parent['name'] . "] " . $name;
            //}

            // Set the category name for the story
            $stories[$idx]['categoryname'] = $category['name'];
            $data['showcategory'] = true;
        } else {
            $stories[$idx]['categoryname'] = '';
        }
    }

    // Create sort by URLs
    if ($sortby != 'category' ) {
        $data['categoryurl'] = xarModURL('newsletter',
                                         'admin',
                                         'viewstory',
                                         array('startnum' => 1,
                                               'sortby' => 'category',
                                               'display' => $display,
                                               'owner' => $owner,
                                               'issueId' => $issueId));
    } else {
        $data['categoryurl'] = '';
    }

    if ($sortby != 'title' ) {
        $data['storytitleurl'] = xarModURL('newsletter',
                                           'admin',
                                           'viewstory',
                                           array('startnum' => 1,
                                                 'sortby' => 'title',
                                                 'display' => $display,
                                                 'owner' => $owner,
                                                 'issueId' => $issueId));
    } else {
        $data['storytitleurl'] = '';
    }

    if ($sortby != 'date' ) {
        $data['storydateurl'] = xarModURL('newsletter',
                                          'admin',
                                          'viewstory',
                                          array('startnum' => 1,
                                                'sortby' => 'date',
                                                'display' => $display,
                                                'owner' => $owner,
                                                'issueId' => $issueId));
    } else {
        $data['storydateurl'] = '';
    }

    if ($sortby != 'owner' ) {
        $data['ownerurl'] = xarModURL('newsletter',
                                             'admin',
                                             'viewstory',
                                             array('startnum' => 1,
                                                   'sortby' => 'owner',
                                                   'display' => $display,
                                                   'owner' => $owner,
                                                   'issueId' => $issueId));
    } else {
        $data['ownerurl'] = '';
    }

    // Get the user id
    $userId = xarSessionGetVar('uid');

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($stories); $i++) {
        $story = $stories[$i];

        $stories[$i]['edittitle'] = xarML('Edit');
        $stories[$i]['deletetitle'] = xarML('Delete');

        if(xarSecurityCheck('EditNewsletter', 0)) { 
            $stories[$i]['editurl'] = xarModURL('newsletter',
                                              'admin',
                                              'modifystory',
                                              array('id' => $story['id']));
        } else {
            $stories[$i]['editurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) { 
            $stories[$i]['deleteurl'] = xarModURL('newsletter',
                                                'admin',
                                                'deletestory',
                                                array('id' => $story['id']));
        } else {
            $stories[$i]['deleteurl'] = '';
        }
    }

    // Add the array of stories to the template variables
    $data['stories'] = $stories;

    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('newsletter', 
                                                  'user', 
                                                  'countstories', 
                                                  array('owner' => $owner,
                                                        'issueId' => $issueId,
                                                        'display' => $display)),
                                    xarModURL('newsletter', 
                                              'admin', 
                                              'viewstory', 
                                              array('startnum' => '%%',
                                                   'sortby' => $sortby,
                                                   'owner' => $owner,
                                                   'display' => $display,
                                                   'issueId' => $issueId)),
                                    xarModGetVar('newsletter', 'itemsperpage'));

    // Return the template variables defined in this function
    return $data;
}

?>
