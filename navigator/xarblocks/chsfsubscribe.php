<?php
/*
 * File: $Id: $
 *
 * CHSF Navigation Block for IAP Subscription
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage chsfnav module
 * @author Richard Cave <caveman : rcave@xaraya.com>
*/

/**
 * initialise block
 */
function chsfnav_chsfsubscribeblock_init()
{
    return true;
}

/**
 * get information on block
 */
function chsfnav_chsfsubscribeblock_info()
{
    // Values
    return array('text_type' => 'Navigation',
                 'module' => 'chsfnav',
                 'text_type_long' => 'Show subscription for Issue Area Publication',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function chsfnav_chsfsubscribeblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewBaseBlocks',0,'Block',"All:$blockinfo[title]:All")) return;

    // Initialize data array
    $data = array();
    
    // Get the cat ids from input
    $cids = xarVarGetCached('Blocks.articles','cids');

    // Make sure there is something to show
    if (empty($cids)) {
        return; // throw back
    }

    // Make sure the issue area publication module is active


    // Get program area and content type cids
    $cids_array = xarModAPIFunc('chsfnav',
                                'user',
                                'parsecids',
                                array('cids' => $cids));

    $program_area_cid = $cids_array[0];
    $content_area_cid = $cids_array[1];
    
    if (!$program_area_cid) {
        return; // throw back
    }

    // Return if we're not on the program area home page
    if ($content_area_cid != xarModGetVar('chsfnav', 'category.default-home')) {
        return; // throw back
    }

    // Get program area information
    $research_cat = xarModAPIFunc('categories',
                                  'user',
                                  'getcat',
                                  array('cid' => $program_area_cid,
                                        'return_itself' => true,
                                        'getparents' => false,
                                        'getchildren' => false));

    // Check for exceptions
    if (!isset($research_cat) && xarExceptionMajor() != XAR_NO_EXCEPTION) {
        return; // throw back
    }
    
    // Get issue area publications
    $publications = xarModAPIFunc('newsletter',
                                  'user',
                                  'get',    
                                   array('phase' => 'publication'));

    // Check for exceptions
    if (!isset($publications) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Initialize variables
    $title = '';
    $description = '';
    $subscribe_link = '';
    $subscribe_text = '';
    $archive_link = '';
    $archive_text = '';
    $found = false;

    // Loop through publications
    foreach ($publications as $publication) {
        // Check if a publication has been assigned to the program area
        if (in_array($program_area_cid, $publication['altcids'])) {
            // Get the publication subscription information
            $description = $publication['description'];

            // Create title 
            $title = xarVarPrepForDisplay($research_cat[0]['name'] . ' News');

            // Get current user
            if (xarUserIsLoggedIn()) {
                // Determine if user has already subscribed to the issue update
                $uid = xarUserGetVar('uid');

                // The user API function is called
                $subscriptions = xarModAPIFunc('newsletter',
                                               'user',
                                               'get',
                                                array('id' => 0, // doesn't matter
                                                      'uid' => $uid,
                                                      'pid' => $publication['id'],
                                                      'phase' => 'subscription'));

                // Has user subscribed?
                if (count($subscriptions) == 0) {
                    // Set subscribe link
                    $subscribe_text = 'Subscribe';
                    $subscribe_link = xarModURL('newsletter',
                                                'user',
                                                'newsubscription');
                } else {
                    // Set modify subscription link
                    $subscribe_text = 'Modify Subscription';
                    $subscribe_link = xarModURL('newsletter',
                                                'user',
                                                'modifysubscription');
                }
            } else {
                // Set subscribe link
                $subscribe_text = 'Subscribe';
                $subscribe_link = xarModURL('newsletter',
                                            'user',
                                            'newsubscription');
            }
            // Set flag
            $found = true;

            // Determine if archives are available for the publication
            if (!$publication['private']) {
                $archive_link = xarModURL('newsletter',
                                          'user',
                                          'viewarchives',
                                          array('publicationId' => $publication['id']));
                $archive_text = 'Archives';
            }

            break;
        }
    }
   
    if ($found) { 
        // Set title and subscription text
        $data['title'] = $title;
        $data['description'] = $description;
        $data['subscribe_link'] = $subscribe_link;
        $data['subscribe_text'] = $subscribe_text;
        $data['archive_link'] = $archive_link;
        $data['archive_text'] = $archive_text;

        // Set blockinfo content
        $blockinfo['content'] = $data;

        if (!empty($blockinfo['content'])) {
            return $blockinfo;
        }
    }
}

?>
