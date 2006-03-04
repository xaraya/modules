<?php
/**
 * Xaraya converter
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage converter Module
 * @author John Cox
*/

/**
 * initialise the converter module
 * @return bool true on success
 */
function converter_init()
{
    // Register Masks
    xarRegisterMask('Adminconverter','All','converter','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the converter module from an old version
 * @return bool true on success
 */
function converter_upgrade($oldversion)
{
    return true;
}

/**
 * delete the converter module
 * @return bool true on success
 */
function converter_delete()
{

    // Remove Masks and Instances
    xarRemoveMasks('Adminconverter');

    // Deletion successful
    return true;
}

?>