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
 * View a list of Newsletter issues
 *
 * @public
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @param 'sortby' sort stories by 'title, 'publication', 'owner' or 'datePublished'
 * @param 'owner' show only logged user stories (1=true, 0=false)
 * @param 'display' show 'published' or 'unpublished' or 'all' stories
 * @param 'publicationId' get issues for a specific publication
 * @returns array
 * @return $data
 */
function newsletter_admin_viewissue($args)
{
    // Extract args
    extract ($args);

    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'datePublished')) return;
    if (!xarVarFetch('owner', 'int:0:1', $owner, 1)) return;
    if (!xarVarFetch('display', 'str:1:', $display, 'unpublished')) return;
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    if (!xarVarFetch('orderby', 'str:1:', $orderby, 'ASC')) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'editmenu');

    // Get the publication title
    $data['publication_title'] = '';
    if ($publicationId) {
        $publication = xarModAPIFunc('newsletter',
                                     'user',
                                     'getpublication',
                                     array('id' => $publicationId));

        // Check for exceptions
        if (!isset($publication) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return; // throw back
        }
        
        $data['publication_title'] = $publication['title'];
    }

    // Set publication 
    $data['publicationId'] = $publicationId;

    // Prepare the array variable that will hold all items for display
    $data['reloadlabel'] = xarVarPrepForDisplay(xarML('Reload'));
    $data['display'] = $display;
    $data['issues'] = array();
    $data['startnum'] = $startnum;
    $data['sortby'] = $sortby;
    $data['owner'] = $owner;
    $data['display'] = $display;
    $data['previewbrowser']   = xarModGetVar('newsletter', 'previewbrowser');

    // If sorting by date published, then sort in descending order
    // so that the latest story is first
    if ($sortby == 'datePublished' ) {
        $orderby = 'DESC';
    }

    // The user API function is called.
    $issues = xarModAPIFunc('newsletter',
                            'user',
                            'get',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('newsletter',
                                                             'itemsperpage'),
                                  'phase' => 'issue',
                                  'sortby' => $sortby,
                                  'owner' => $owner,
                                  'display' => $display,
                                  'orderby' => $orderby,
                                  'publicationId' => $publicationId));

    // Check for exceptions
    if (!isset($issues) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return; // throw back
    }

    // Get the user id
    $userId = xarSessionGetVar('uid');

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($issues); $i++) {
        $issue = $issues[$i];

        $issues[$i]['edittitle'] = xarML('Edit Issue');
        $issues[$i]['deletetitle'] = xarML('Delete Issue');
        $issues[$i]['previewtitle'] = xarML('Preview');
        $issues[$i]['publishtitle'] = xarML('Publish');
        $issues[$i]['newstorytitle'] = xarML('New Story');
        $issues[$i]['editstoriestitle'] = xarML('Edit Stories');

        if (xarSecurityCheck('EditNewsletter', 0)) { 
            $issues[$i]['editurl'] = xarModURL('newsletter',
                                               'admin',
                                               'modifyissue',
                                               array('id' => $issue['id']));

            $issues[$i]['editstoriesurl'] = xarModURL('newsletter',
                                                      'admin',
                                                      'viewstory',
                                                      array('issueId' => $issue['id'],
                                                            'owner' => $owner,
                                                            'display' =>$display));

            $issues[$i]['newstoryurl'] = xarModURL('newsletter',
                                                   'admin',
                                                   'newstory',
                                                   array('publicationId' => $publicationId,
                                                         'issueId' => $issue['id']));

        } else {
            $issues[$i]['editurl'] = '';
            $issues[$i]['newstoryurl'] = '';
            $issues[$i]['editstoriesurl'] = '';
        }

        if(xarSecurityCheck('ReadNewsletter', 0)) { 
            $issues[$i]['previewurl'] = xarModURL('newsletter',
                                                  'admin',
                                                  'previewissue',
                                                  array('issueId' => $issue['id']));
        } else {
            $issues[$i]['previewurl'] = '';
        }

        if(xarSecurityCheck('AdminNewsletter', 0)) { 
            // Check if the issue has been published already
            if ($issue['datePublished']['timestamp'] == 0) {
                $issues[$i]['publishurl'] = xarModURL('newsletter',
                                                      'admin',
                                                      'publishissue',
                                                      array('issueId' => $issue['id']));
            } else {
                $issues[$i]['publishurl'] = '';
            }
        } else {
            $issues[$i]['publishurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) { 
            $issues[$i]['deleteurl'] = xarModURL('newsletter',
                                                 'admin',
                                                 'deleteissue',
                                                 array('id' => $issue['id']));
        } else {
            $issues[$i]['deleteurl'] = '';
        }
    }

    // Add the array of issues to the template variables
    $data['issues'] = $issues;

    // Create sort by URLs
    if ($sortby != 'title' ) {
        $data['issuetitleurl'] = xarModURL('newsletter',
                                           'admin',
                                           'viewissue',
                                           array('startnum' => 1,
                                                 'sortby' => 'title',
                                                 'display' => $display,
                                                 'owner' => $owner,
                                                 'publicationId' => $publicationId));
    } else {
        $data['issuetitleurl'] = '';
    }

    if ($sortby != 'publication' ) {
        $data['publicationurl'] = xarModURL('newsletter',
                                            'admin',
                                            'viewissue',
                                            array('startnum' => 1,
                                                  'sortby' => 'publication',
                                                  'display' => $display,
                                                  'owner' => $owner,
                                                  'publicationId' => $publicationId));
    } else {
        $data['publicationurl'] = '';
    }

    if ($sortby != 'owner' ) {
        $data['issueownerurl'] = xarModURL('newsletter',
                                           'admin',
                                           'viewissue',
                                           array('startnum' => 1,
                                                 'sortby' => 'owner',
                                                 'display' => $display,
                                                 'owner' => $owner,
                                                 'publicationId' => $publicationId));
    } else {
        $data['issueownerurl'] = '';
    }

    if ($sortby != 'datePublished' ) {
        $data['issuedateurl'] = xarModURL('newsletter',
                                          'admin',
                                          'viewissue',
                                          array('startnum' => 1,
                                                'sortby' => 'datePublished',
                                                'display' => $display,
                                                'owner' => $owner,
                                                'publicationId' => $publicationId));
    } else {
        $data['issuedateurl'] = '';
    }

    // Create pagination
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('newsletter', 
                                                  'user', 
                                                  'countissues', 
                                                  array('owner' => $owner,
                                                        'publicationId' => $publicationId,
                                                        'display' => $display,
                                                        'external' => 0)),
                                    xarModURL('newsletter', 
                                              'admin', 
                                              'viewissue', 
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
