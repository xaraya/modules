<?php

/**
 * File: $Id$
 *
 * Configuration screen
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @link  link to where more info can be found
 * @author author name <author@email> (this tag means responsible person)
*/


function bkview_admin_modifyconfig() 
{
    $data = array();

    // Check whether the search functionality is enabled
    $data['search_enabled'] = 0;
    if(xarModIsHooked('search','bkview')) {
        $data['search_enabled'] = 1;
    }
                                     
    // Assemble the template data
    $data['pageinfo'] = xarML('BKview configuration');
    $data['hooks'] =  xarModCallHooks('module','modifyconfig','bkview',array());
    $data['updatelabel']= xarML('Update');
    $data['formaction'] = xarModUrl('bkview','admin','updateconfig');
    return $data;
}
?>
