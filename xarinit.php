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
 * Initialize the phpLDAPadmin module
 */
function phpldapadmin_init()
{
   xarRegisterMask('OverviewphpLDAPadmin','All','phpldapadmin','All','All','ACCESS_OVERVIEW');
   xarRegisterMask('AdminphpLDAPadmin','All','phpldapadmin','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the phpLDAPadmin module from an old version
 */
function phpldapadmin_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.9.0':
        case '0.9.1':
        case '0.9.2':
            break;
        case '0.9.3':
            // Code to upgrade from version '0.9.3' goes here
            break;
    }

    // Update successful
    return true;
}

/**
 * Delete the phpLDAPadmin module
 */
function phpldapadmin_delete()
{
    // Remove masks
    xarRemoveMasks('phpldapadmin');

    // Deletion successful
    return true;
}

?>
