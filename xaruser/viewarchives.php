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
 * View a list of Newsletter past issues
 *
 * @public
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @param 'sortby' sort stories by 'title, 'publication' or 'owner'
 * @param 'owner' show only logged user stories (1=true, 0=false)
 * @param 'publicationId' get issues for a specific publication
 * @param 'display' show 'published' or 'unpublished' or 'all' stories
 * @returns array
 * @return $data
 */
function newsletter_user_viewarchives($args)
{
    // Extract args
    extract ($args);

    // Security check
    //if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'datePublished')) return;
    if (!xarVarFetch('orderby', 'str:1:', $orderby, 'ASC')) return;

    // Display will always be published
    if (!xarVarFetch('display', 'str:1:', $display, 'published')) return;

    // Owner should always be set to zero
    if (!xarVarFetch('owner', 'int:0:1', $owner, 0)) return;


    // If $sortby is 'datePublished', then set orderby to DESC so newest
    // issues are on top
    if ($sortby == 'datePublished') {
        $orderby = 'DESC';
    }
    // Get the user menu
    $data = xarModAPIFunc('newsletter', 'user', 'menu');

    // Prepare the array variable that will hold all items for display
    $data['publicationId'] = $publicationId;
    $data['startnum'] = $startnum;
    $data['issues'] = array();
    $data['sortby'] = $sortby;
    $data['owner'] = $owner;
    $data['display'] = $display;
    $data['previewbrowser']   = xarModGetVar('newsletter', 'previewbrowser');
    
    // Get the list of publications
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('phase' => 'publication',
                                        'sortby' => 'title'));
   
    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    $data['publications'] = $publications;

    // Get the list of issues
    $issues = xarModAPIFunc('newsletter',
                            'user',
                            'get',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('newsletter',
                                                             'itemsperpage'),
                                  'phase' => 'issue',
                                  'sortby' => $sortby,
                                  'orderby' => $orderby,
                                  'owner' => $owner,
                                  'display' => 'published',
                                  'publicationId' => $publicationId,
                                  'external' => true));

    // Check for exceptions
    if (!isset($issues) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Make sure there are issues to show
    if (empty($issues)) {
        // No issues yet, show just return empty issue list
        $data['issues'] = $issues;
        return $data;
    }

    // Loop through issues and check if external or private
    for ($idx = 0; $idx < count($issues); $idx++) {
        if(xarSecurityCheck('ReadNewsletter', 0)) { 
            // Create preview title and url
            $issues[$idx]['previewtitle'] = xarML('Preview');
            $issues[$idx]['previewurl'] = xarModURL('newsletter',
                                                    'user',
                                                    'previewissue',
                                                    array('issueId' => $issues[$idx]['id']));
        }
    }

    // Add the array of issues to the template variables
    $data['issues'] = $issues;

    // Create sort by URLs
    if ($sortby != 'title' ) {
        $data['issuetitleurl'] = xarModURL('newsletter',
                                           'user',
                                           'viewarchives',
                                           array('startnum' => 1,
                                                 'sortby' => 'title',
                                                 'display' => $display,
                                                 'owner' => $owner,
                                                 'publicationId' => $publicationId));
    } else {
        $data['issuetitleurl'] = '';
    }

    // Sort by publication
    if ($sortby != 'publication' ) {
        $data['publicationurl'] = xarModURL('newsletter',
                                             'user',
                                             'viewarchives',
                                             array('startnum' => 1,
                                                   'sortby' => 'publication',
                                                   'display' => $display,
                                                   'owner' => $owner,
                                                   'publicationId' => $publicationId));
    } else {
        $data['publicationurl'] = '';
    }

    // Sort by date published
    if ($sortby != 'datePublished' ) {
        $data['datepublishedurl'] = xarModURL('newsletter',
                                              'user',
                                              'viewarchives',
                                              array('startnum' => 1,
                                                    'sortby' => 'datePublished',
                                                    'display' => $display,
                                                    'owner' => $owner,
                                                    'publicationId' => $publicationId));
    } else {
        $data['datepublishedurl'] = '';
    }

    // Create pagination
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('newsletter', 
                                                  'user', 
                                                  'countissues', 
                                                  array('owner' => $owner,
                                                        'publicationId' => $publicationId,
                                                        'display' => $display,
                                                        'external' => 1)),
                                    xarModURL('newsletter', 
                                              'user', 
                                              'viewarchives', 
                                              array('startnum' => '%%',
                                                    'sortby' => $sortby,
                                                    'owner' => $owner,
                                                    'display' => $display,
                                                    'publicationId' => $publicationId)),
                                    xarModGetVar('newsletter', 'itemsperpage'));

    // Return the template variables defined in this function
    return $data;
}

?>
