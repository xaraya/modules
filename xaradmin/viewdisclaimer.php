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
 * View a list of Newsletter disclaimers
 *
 * @public
 * @author Richard Cave
 * @param 'startnum' starting number to display
 * @returns array
 * @return $data
 */
function newsletter_admin_viewdisclaimer($args)
{
    // Extract args
    extract ($args);

    // Get parameters from the input
    if (!xarVarFetch('startnum', 'int:0:', $startnum, 1)) return;

    // Get the admin menu
    $data['menu'] = xarModFunc('newsletter', 'admin', 'editmenu');

    // Prepare the array variable that will hold all items for display
    $data['items'] = array();

    // Options label
    $data['title'] = xarVarPrepForDisplay(xarML('View Disclaimers'));

    // The user API function is called.
    $disclaimers = xarModAPIFunc('newsletter',
                                 'user',
                                 'get',
                                 array('startnum' => $startnum,
                                       'numdisclaimers' => xarModGetVar('newsletter',
                                                                        'disclaimersperpage'),
                                       'phase' => 'disclaimer'));

    // Check for exceptions
    if (!isset($disclaimers) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($disclaimers); $i++) {
        $disclaimer = $disclaimers[$i];

        $disclaimers[$i]['edittitle'] = xarML('Edit');
        $disclaimers[$i]['deletetitle'] = xarML('Delete');

        if(xarSecurityCheck('EditNewsletter', 0)) { 
            $disclaimers[$i]['editurl'] = xarModURL('newsletter',
                                                    'admin',
                                                    'modifydisclaimer',
                                                    array('id' => $disclaimer['id']));
        } else {
            $disclaimers[$i]['editurl'] = '';
        }

        if(xarSecurityCheck('DeleteNewsletter', 0)) { 
            $disclaimers[$i]['deleteurl'] = xarModURL('newsletter',
                                                      'admin',
                                                      'deletedisclaimer',
                                                      array('id' => $disclaimer['id']));
        } else {
            $disclaimers[$i]['deleteurl'] = '';
        }
    }

    // Add the array of disclaimers to the template variables
    $data['disclaimers'] = $disclaimers;

    // Return the template variables defined in this function
    return $data;
}

?>
