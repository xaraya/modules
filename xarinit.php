<?php
/**
 * File: $Id$
 *
 * simplepie init function. Xaraya 1.1.5 seems to need this.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage simplepie
 * @author Jason Judge
 */

/**
 * initialise the simplepie module
 */
function simplepie_init()
{
    // Initialisation successful
    return true;
}

/**
 * upgrade the simplepie module
 */
function simplepie_upgrade($oldversion)
{
    switch($oldversion){
        case '0.1.0':
        break;
    }
    return true;
}

/**
 * delete the simplepie module
 */
function simplepie_delete()
{
    // Deletion successful
    return true;
}

?>