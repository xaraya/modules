<?php
/**
 * File: $Id$
 *
 * Vanilla Forums init function. Xaraya 1.1.5 seems to need this.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage vanilla
 * @author Jason Judge
 */

/**
 * install the vanilla module
 */
function vanilla_init()
{
    // Set the base directory for the Vanilla install, relative to the Xaraya entry-point.
    xarModSetVar('vanilla', 'basepath', 'forums');

    // Initialisation successful
    return true;
}

/**
 * upgrade the vanilla module
 */
function vanilla_upgrade($oldversion)
{
    switch($oldversion){
       case '0.1.0':
         
        break;

    }
    return true;
}

/**
 * delete the vanilla module
 */
function vanilla_delete()
{

    // Deletion successful
    return true;
}

?>