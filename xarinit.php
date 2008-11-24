<?php
/**
 * File: $Id$
 *
 * jQuery init function. Xaraya 1.1.5 seems to need this.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage jQuery
 * @author Jason Judge
 */

/**
 * initialise the jQuery module
 */
function jquery_init()
{
    // Initialisation successful
    return true;
}

/**
 * upgrade the jQuery module
 */
function jquery_upgrade($oldversion)
{
    switch($oldversion){
        case '0.1.1':
        break;
    }
    return true;
}

/**
 * delete the jQuery module
 */
function jquery_delete()
{
    // Deletion successful
    return true;
}

?>