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
 * Display subscriptions based on publication or name/email
 *
 * @public
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @param 'search' the type of search to perform ('publication', 'email' or 'name')
 * @param 'publicationId' the id of the publication to search in
 * @param 'searchname' the email or name to search for
 * @returns array
 * @return $data
 */
function newsletter_admin_viewsubscription($args)
{
    // Extract args
    extract ($args);

    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters from input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('search', 'str:1:', $search, 'name')) return;
    if (!xarVarFetch('publicationId', 'id', $publicationId, 0)) return;
    if (!xarVarFetch('searchname', 'str:1:', $searchname, '')) return;

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'subscriptionmenu');

    // Set parameters so we can return to this menu after a delete
    $data['startnum'] = $startnum;
    $data['search'] = $search;
    $data['publicationId'] = $publicationId;
    $data['searchname'] = $searchname;

    // Switch to requested view
    switch(strtolower($search)) {
        case 'publication':
            // Search by the publication ID
            $searchargs =  array('search' => 'publication',
                                 'searchname' => $searchname,
                                 'pid' => $publicationId,
                                 'startnum' => $startnum,
                                 'numitems' => xarModGetVar('newsletter',
                                                            'subscriptionsperpage'));
            break;

        case 'email':
            // Search by email
            $searchargs =  array('search' => 'email',
                                 'searchname' => $searchname,
                                 'startnum' => $startnum,
                                 'numitems' => xarModGetVar('newsletter',
                                                            'subscriptionsperpage'));
            break;

        case 'name':
        default:
            // Search by uname, name or email
            $searchargs =  array('search' => 'name',
                                 'searchname' => $searchname,
                                 'startnum' => $startnum,
                                 'numitems' => xarModGetVar('newsletter',
                                                            'subscriptionsperpage'));
        break;
    }

    // Search the subscription table - this is tied to the roles table
    $subscriptions = xarModAPIFunc('newsletter',
                                   'admin',
                                   'searchsubscription',
                                   $searchargs);

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($subscriptions); $i++) {
        $subscription = $subscriptions[$i];

        $subscriptions[$i]['edittitle'] = xarML('Edit');
        $subscriptions[$i]['deletetitle'] = xarML('Delete');

        // Only allow the owner or user with appropriate privileges
        // to edit/delete an subscription
        if(xarSecurityCheck('EditNewsletter', 0)) { 
            if ($subscription['type'] == 0) {
                // Modify a subscription
                $subscriptions[$i]['editurl'] = xarModURL('newsletter',
                                                          'admin',
                                                          'modifysubscription',
                                                          array('uid' => $subscription['uid']));
            } else {
                // Modify an alternative subscription
                $subscriptions[$i]['editurl'] = xarModURL('newsletter',
                                                          'admin',
                                                          'modifyaltsubscription',
                                                          array('id' => $subscription['uid']));
            }
        } else {
            $subscriptions[$i]['editurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) { 
            if ($subscription['type'] == 0) {
                // Delete a subscription
                $subscriptions[$i]['deleteurl'] = xarModURL('newsletter',
                                                            'admin',
                                                            'deletesubscription',
                                                            array('uid' => $subscription['uid'],
                                                                  'pid' => $subscription['pid']));
            } else {
                // Delete an alternative subscription
                $subscriptions[$i]['deleteurl'] = xarModURL('newsletter',
                                                            'admin',
                                                            'deletealtsubscription',
                                                            array('id' => $subscription['uid']));
            }
        } else {
            $subscriptions[$i]['deleteurl'] = '';
        }
    }
/*
    // Search the alternative subscription table - this is tied a
    // subscription email address
    $altsubscriptions = xarModAPIFunc('newsletter',
                                      'admin',
                                      'searchaltsubscription',
                                      $searchargs);

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($altsubscriptions); $i++) {
        $subscription = $altsubscriptions[$i];

        $altsubscriptions[$i]['edittitle'] = xarML('Edit');
        $altsubscriptions[$i]['deletetitle'] = xarML('Delete');

        // Only allow the owner or user with appropriate privileges
        // to edit/delete an subscription
        if(xarSecurityCheck('EditNewsletter', 0)) { 
            $altsubscriptions[$i]['editurl'] = xarModURL('newsletter',
                                                      'admin',
                                                      'modifyaltsubscription',
                                                      array('id' => $subscription['id']));
        } else {
            $altsubscriptions[$i]['editurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) { 
            $altsubscriptions[$i]['deleteurl'] = xarModURL('newsletter',
                                                        'admin',
                                                        'deletealtsubscription',
                                                        array('id' => $subscription['id']));
        } else {
            $altsubscriptions[$i]['deleteurl'] = '';
        }
    }

    // Add the array of items to the template variables
    $data['subscriptions'] = array_merge($subscriptions, $altsubscriptions);
*/
    $data['subscriptions'] = $subscriptions;

    // Sort arrays
    switch ($search) {
        case 'name':
            usort( $data['subscriptions'], "nwsltr_vs__sortsubscriptionbyname" );
            break;
        case 'email':
            usort( $data['subscriptions'], "nwsltr_vs__sortsubscriptionbyemail" );
            break;
        case 'username':
            usort( $data['subscriptions'], "nwsltr_vs__sortsubscriptionbyusername" );
            break;
        case 'publication':
            usort( $data['subscriptions'], "nwsltr_vs__sortsubscriptionbypublication" );
            break;
    }

    // Create sort by URLs
    if ($search != 'name' ) {
        $data['nameurl'] = xarModURL('newsletter',
                                     'admin',
                                     'viewsubscription',
                                     array('startnum' => 1,
                                           'search' => 'name',
                                           'publicationId' => $publicationId,
                                           'searchname' => $searchname));
    } else {
        $data['nameurl'] = '';
    }

    if ($search != 'username' ) {
        $data['usernameurl'] = xarModURL('newsletter',
                                         'admin',
                                         'viewsubscription',
                                         array('startnum' => 1,
                                               'search' => 'uname',
                                               'publicationId' => $publicationId,
                                               'searchname' => $searchname));
    } else {
        $data['usernameurl'] = '';
    }

    if ($search != 'email' ) {
        $data['emailurl'] = xarModURL('newsletter',
                                      'admin',
                                      'viewsubscription',
                                      array('startnum' => 1,
                                            'search' => 'email',
                                            'publicationId' => $publicationId,
                                            'searchname' => $searchname));
    } else {
        $data['emailurl'] = '';
    }

    if ($search != 'publication' ) {
        $data['publicationurl'] = xarModURL('newsletter',
                                            'admin',
                                            'viewsubscription',
                                            array('startnum' => 1,
                                                  'search' => 'publication',
                                                  'publicationId' => $publicationId,
                                                  'searchname' => $searchname));
    } else {
        $data['publicationurl'] = '';
    }

    // Add pagination
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('newsletter',
                                                  'admin',
                                                  'countsubscriptions',
                                                  array('id' => $publicationId)),
                                    xarModURL('newsletter', 
                                              'admin', 
                                              'viewsubscription', 
                                              array('startnum' => '%%',
                                                    'search' => $search,
                                                    'publicationId' => $publicationId,
                                                    'searchname' => $searchname)),
                                    xarModGetVar('newsletter', 'subscriptionsperpage'));

    // Return the template variables defined in this function
    return $data;
}

/**
 * Comparision functions for sorting by name
 *
 * @private
 * @author Richard Cave
 * @param a multi-dimensional array
 * @param b multi-dimensional array
 * @returns strcmp
 */
function nwsltr_vs__sortsubscriptionbyname ($a, $b) 
{
    $cmp1 = trim(strtolower($a['name']));
    $cmp2 = trim(strtolower($b['name']));
    return strcmp($cmp1, $cmp2);
}

function nwsltr_vs__sortsubscriptionbyusername ($a, $b) 
{
    $cmp1 = trim(strtolower($a['uname']));
    $cmp2 = trim(strtolower($b['uname']));
    return strcmp($cmp1, $cmp2);
}

function nwsltr_vs__sortsubscriptionbyemail ($a, $b) 
{
    $cmp1 = trim(strtolower($a['email']));
    $cmp2 = trim(strtolower($b['email']));
    return strcmp($cmp1, $cmp2);
}

function nwsltr_vs__sortsubscriptionbypublication ($a, $b) 
{
    $cmp1 = trim(strtolower($a['title']));
    $cmp2 = trim(strtolower($b['title']));
    return strcmp($cmp1, $cmp2);
}




?>
