<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 *
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
 */
function converter_init()
{
    // Register Masks
    xarRegisterMask('Adminconverter','All','converter','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the smiley module from an old version
 */
function converter_upgrade($oldversion)
{
    return true;
}

/**
 * delete the smiley module
 */
function converter_delete()
{

    // Remove Masks and Instances
    xarRemoveMasks('Adminconverter');

    // Deletion successful
    return true;
}

?>