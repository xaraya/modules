<?php
/*
 * File: $Id: $
 *
 * DaveDAP 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team 
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage davedap module
 * @author Richard Cave <rcave@schwabfoundation.org : rcave@xaraya.com>
 * @link http://xavier.schwabfoundation.org
*/


/**
 * Initialize the DaveDAP module
 */
function davedap_init()
{
   xarRegisterMask('OverviewDaveDAP','All','davedap','All','All','ACCESS_OVERVIEW');
   xarRegisterMask('AdminDaveDAP','All','davedap','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the DaveDAP module from an old version
 */
function davedap_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        //case 1.0:
            // Code to upgrade from version 1.0 goes here
            //break;
        //case 2.0:
            // Code to upgrade from version 2.0 goes here
            //break;
        default:
            break;
    }

    // Update successful
    return true;
}

/**
 * Delete the DaveDAP module
 */
function davedap_delete()
{
    // Remove masks
    xarRemoveMasks('davedap');

    // Deletion successful
    return true;
}

?>
