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
 * Display subscriptions based on publication or name/email
 *
 * @public
 * @author Richard Cave
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
    if (!xarVarFetch('search', 'str:1:', $search, 'name')) return;
    if (!xarVarFetch('publicationId', 'id', $publicationId, 0)) return;
    if (!xarVarFetch('searchname', 'str:1:', $searchname, '')) return;

    // If searching by publication, make sure something was selected
    if ($search == 'publication' && $publicationId == 0) {
        $msg = xarML('You must choose a publication to search.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get the admin edit menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'subscriptionmenu');

    // Set parameters so we can return to this menu after a delete
    $data['search'] = $search;
    $data['publicationId'] = $publicationId;
    $data['searchname'] = $searchname;

    // Switch to requested view
    switch(strtolower($search)) {
        case 'publication':
            // Search by the publication ID
            if ($publicationId != 0) {
                $searchargs =  array('search' => 'publication',
                                     'searchname' => $searchname,
                                     'pid' => $publicationId);
            }
            break;

        case 'email':
            // Search by email
            $searchargs =  array('search' => 'email',
                                 'searchname' => $searchname);
            break;

        case 'name':
        default:
            // Search by uname, name and email
            $searchargs =  array('search' => 'name',
                                 'searchname' => $searchname);
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
            $subscriptions[$i]['editurl'] = xarModURL('newsletter',
                                                      'admin',
                                                      'modifysubscription',
                                                      array('uid' => $subscription['uid']));
        } else {
            $subscriptions[$i]['editurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) { 
            $subscriptions[$i]['deleteurl'] = xarModURL('newsletter',
                                                        'admin',
                                                        'deletesubscription',
                                                        array('uid' => $subscription['uid'],
                                                            'pid' => $subscription['pid']));
        } else {
            $subscriptions[$i]['deleteurl'] = '';
        }
    }

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

    // Sort arrays by name
    usort( $data['subscriptions'], "nwsltr_vs__sortsubscriptionbyname" );

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


?>
