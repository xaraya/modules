<?php
/**
 * File: $Id: s.xarinit.php 1.27 03/01/17 15:18:04-08:00 rcave@lxwdev-1.schwabfoundation.org $
 *
 * Initialise the htmlarea module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage HTMLArea Module
 * @author Marc Lutolf
 */

/**
 * Initialise the htmlarea module
 *
 * @access public
 * @param none
 * @returns bool true
 */
function htmlarea_init()
{
    return true;
}

/**
 * Upgrade the htmlarea module from an old version
 *
 * @access public
 * @param none $
 * @returns bool
 */
function htmlarea_activate()
{
    // Activate successful
    return true;
}

/**
 * Upgrade the htmlarea module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 * @raise DATABASE_ERROR
 */
function htmlarea_upgrade($oldVersion)
{
    // Update successful
    return true;
}

/**
 * Delete the htmlarea module
 *
 * @access public
 * @param none $
 * @returns bool true
 */
function htmlarea_delete()
{
    return true;
}

?>