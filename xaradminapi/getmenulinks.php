<?php
/*
 * File: $Id: $
 *
 * phpLDAPadmin 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage phpldapadmin module
 * @author Richard Cave <rcave@schwabfoundation.org : rcave@xaraya.com>
 * @link http://xavier.schwabfoundation.org
*/


/**
 * Utility function to pass individual menu items to the main menu
 *
 * @author Richard Cave
 * @returns array
 * @return $menu
 */
function phpldapadmin_adminapi_getmenulinks()
{
    if(xarSecurityCheck('AdminphpLDAPadmin', 0)) {

        $menulinks[] = Array('url'   => xarModURL('phpldapadmin',
                                                  'admin',
                                                  'phpldapadmin'),
                              'title' => xarML('Run phpLDAPadmin'),
                              'label' => xarML('Run phpLDAPadmin'));
    } else {
        $menulinks = '';
    }

    // Return the array containing the menu links 
    return $menulinks;
}

?>
