<?php
/**
 *
 * Initialise the kupu module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage kupu Module
 * @author Marc Lutolf
 */

/**
 * Initialise the kupu module
 *
 * @access public
 * @param none
 * @returns bool true
 */
function kupu_init()
{
    return true;
}

/**
 * Upgrade the kupu module from an old version
 *
 * @access public
 * @param none $
 * @returns bool
 */
function kupu_activate()
{
    // Activate successful
    return true;
}

/**
 * Upgrade the kupu module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 * @raise DATABASE_ERROR
 */
function kupu_upgrade($oldVersion)
{
    // Update successful
    return true;
}

/**
 * Delete the kupu module
 *
 * @access public
 * @param none $
 * @returns bool true
 */
function kupu_delete()
{
    return true;
}

?>