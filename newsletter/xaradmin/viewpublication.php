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
 * View a list of Newsletter publications
 *
 * @public
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @param 'sortby' sort stories by 'title, 'category' or 'owner'
 * @param 'owner' show only logged user stories (1=true, 0=false)
 * @returns array
 * @return $data
 */
function newsletter_admin_viewpublication($args)
{
    // Extract args
    extract ($args);

    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'title')) return;
    if (!xarVarFetch('owner', 'int:0:1', $owner, 0)) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'editmenu');

    // Prepare the array variable that will hold all items for display
    $data['items'] = array();
    $data['startnum'] = $startnum;
    $data['sortby'] = $sortby;

    // Specify some labels for display
    $data['ownerlabel'] = xarVarPrepForDisplay(xarML('Owner Name'));
    $data['optionslabel']=xarVarPrepForDisplay(xarML('Options'));

    // The user API function is called.
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('newsletter',
                                                                   'itemsperpage'),
                                        'phase' => 'publication',
                                        'sortby' => $sortby));

    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get category name and parent category name
    $data['showcategory'] = false;
    for ($idx = 0; $idx < count($publications); $idx++) {
            
        if ($publications[$idx]['cid'] != 0 ) {
            $category = xarModAPIFunc('categories',
                                      'user',
                                      'getcatinfo', // may need to change to getcat
                                      Array('cid' => $publications[$idx]['cid']));

            // Check for exceptions
            if (!isset($category) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return;
            // Set the category name for the story
            $publications[$idx]['categoryname'] = $category['name'];
            $data['showcategory'] = true;
        } else {
            $publications[$idx]['categoryname'] = '';
        }
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($publications); $i++) {
        $item = $publications[$i];

        $publications[$i]['edittitle'] = xarML('Edit');
        $publications[$i]['deletetitle'] = xarML('Delete');
        $publications[$i]['newissuestitle'] = xarML('Add Issue');
        $publications[$i]['editissuestitle'] = xarML('Edit Issues');

        // Check if this is a publication and only 
        // allow the owner to edit/delete their own publication
        if(xarSecurityCheck('EditNewsletter', 0)) { 
            $publications[$i]['editurl'] = xarModURL('newsletter',
                                                     'admin',
                                                     'modifypublication',
                                                     array('id' => $item['id']));

            $publications[$i]['newissuesurl'] = xarModURL('newsletter',
                                                           'admin',
                                                           'newissue',
                                                           array('publicationId' => $item['id']));
            $publications[$i]['editissuesurl'] = xarModURL('newsletter',
                                                           'admin',
                                                           'viewissue',
                                                           array('publicationId' => $item['id']));

        } else {
            $publications[$i]['editurl'] = '';
            $publications[$i]['newissuesurl'] = '';
            $publications[$i]['editissuesurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) {
            $publications[$i]['deleteurl'] = xarModURL('newsletter',
                                                       'admin',
                                                       'deletepublication',
                                                       array('id' => $item['id']));
        } else  {
            $publications[$i]['deleteurl'] = '';
        }
    }

    // Create sort by URLs
    if ($sortby != 'category' ) {
        $data['categoryurl'] = xarModURL('newsletter',
                                         'admin',
                                         'viewpublication',
                                         array('startnum' => 1,
                                               'sortby' => 'category'));
    } else {
        $data['categoryurl'] = '';
    }

    if ($sortby != 'title' ) {
        $data['publicationtitleurl'] = xarModURL('newsletter',
                                                 'admin',
                                                 'viewpublication',
                                                 array('startnum' => 1,
                                                       'sortby' => 'title'));
    } else {
        $data['publicationtitleurl'] = '';
    }

    if ($sortby != 'owner' ) {
        $data['publicationownerurl'] = xarModURL('newsletter',
                                                 'admin',
                                                 'viewpublication',
                                                 array('startnum' => 1,
                                                       'sortby' => 'owner'));
    } else {
        $data['publicationownerurl'] = '';
    }

    // Add the array of publications to the template variables
    $data['publications'] = $publications;

    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('newsletter', 
                                                  'user', 
                                                  'countitems', 
                                                  array('phase' => 'publication')),
                                    xarModURL('newsletter', 
                                              'admin', 
                                              'viewpublication', 
                                              array('startnum' => '%%',
                                                   'sortby' => $sortby)),
                                    xarModGetVar('newsletter', 'itemsperpage'));


    // Return the template variables defined in this function
    return $data;
}


?>
