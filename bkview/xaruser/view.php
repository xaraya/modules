<?php

/**
 * File: $Id$
 *
 * View function for bkview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkviewe
 * @author Marcel van der Boom <marcel@hsdev.com>
*/

/**
 * view a list of items
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 */
function bkview_user_view()
{
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    $data['items'] = array();
    // The main function displays  a list of registerd repositories
    if (!xarSecurityCheck('ViewAllRepositories')) return;
    
    $items = xarModAPIFunc('bkview', 'user','getall', array());
    if (!isset($items) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    
    foreach ($items as $item) {
        if (xarSecurityCheck('ViewAllRepositories')) {
            $item['link'] = xarModURL('bkview',    'user',    'display',array('repoid' => $item['repoid']));
        } else {
            $item['link'] = '';
        }
        
        // Clean up the item text before display
        $item['reponame'] = xarVarPrepForDisplay($item['reponame']);
        $data['items'][] = $item;
    }

    $data['pageinfo']=xarML('View available bitkeeper repositories');
    return $data;
}
?>
