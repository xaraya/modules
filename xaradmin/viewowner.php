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
 * View a list of Newsletter owners
 *
 * @author Richard Cave
 * @param $startnum starting number to display
 * @param $sortby sort ('name' or group')
 * @returns array
 * @return $data
 */
function newsletter_admin_viewowner()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get parameters
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, 'name')) return;

    // Get the admin menu
    $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Prepare the array variable that will hold all items for display
    $data['sortby'] = $sortby;

    // Get list of owners
    $owners = xarModAPIFunc('newsletter',
                            'user',
                            'getowners',
                            array('startnum' => $startnum,
                                  'numowners' => xarModGetVar('newsletter',
                                                                                 'ownersperpage')));

    // Check for exceptions
    if (!isset($owners) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Create sort by URLs
    if ($sortby != 'name' ) {
        $data['nameurl'] = xarModURL('newsletter',
                                         'admin',
                                         'viewowner',
                                         array('sortby' => 'name'));
    } else {
        $data['nameurl'] = '';
    }

    if ($sortby != 'group' ) {
        $data['groupurl'] = xarModURL('newsletter',
                                         'admin',
                                         'viewowner',
                                         array('sortby' => 'group'));
    } else {
        $data['groupurl'] = '';
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($owners); $i++) {
        $item = $owners[$i];

        // Get the group for the user 
        $role = xarModAPIFunc('roles',
                              'user',
                              'get',
                               array('uid' => $item['rid'],
                                     'type' => 1));
        
        $owners[$i]['group'] = $role['name'];

        // Edit URL
        $owners[$i]['editurl'] = xarModURL('newsletter',
                                          'admin',
                                          'modifyowner',
                                          array('id' => $item['id']));

        $owners[$i]['edittitle'] = xarML('Edit');

        // Delete URL
        $owners[$i]['deleteurl'] = xarModURL('newsletter',
                                            'admin',
                                            'deleteowner',
                                            array('id' => $item['id']));

        $owners[$i]['deletetitle'] = xarML('Delete');
    }

    // If $sortby is group, then resort array by group name
    // since default is to sort by owner name
    if ($sortby == 'group') {
        usort( $owners, "newsletter_admin__cmpgroup" );
    }

    // Add the array of owners to the template variables
    $data['owners'] = $owners;

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
function newsletter_admin__cmpgroup ($a, $b) 
{
    $cmp1 = trim(strtolower($a['group']));
    $cmp2 = trim(strtolower($b['group']));
    return strcmp($cmp1, $cmp2);
}

?>
