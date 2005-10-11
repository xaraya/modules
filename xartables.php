<?php
/**
    Helpdesk
 
    @package Xaraya eXtensible Management System
    @copyright (C) 2003-2004 by Envision Net, Inc.
    @license GPL <http://www.gnu.org/licenses/gpl.html>
    @link http://www.envisionnet.net/
 
    @subpackage Helpdesk module
    @author Brian McGilligan <brian@envisionnet.net>
*/

// Based On:
/********************************************************/
/* Dimensionquest Help Desk                             */
/*  Development by:                                     */
/*     Burke Azbill - burke@dimensionquest.net          */
/*                                                      */
/* This program is opensource so you can do whatever    */
/* you want with it.                                    */
/*                                                      */
/* http://www.dimensionquest.net                           */
/********************************************************/

/**
* This function is called internally by the core whenever the module is
* loaded.  It adds in the information
*/
function helpdesk_xartables()
{
    // Initialise table array
    $xartable = array();

    // Get the name for the table.  This is not necessary
    // but helps in the following statements and keeps them readable
    $hd = xarConfigGetVar('prefix') . '_hd_';

    // Set the table name
    $xartable['helpdesk'] = $hd;

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    // Name for helpdesk database entities
    $coprefix = xarConfigGetVar('prefix') . '_helpdesk';

    $xartable['helpdesk_tickets'] = $coprefix.'_tickets';                
    $xartable['helpdesk_status'] = $coprefix . '_status';
    $xartable['helpdesk_source'] = $coprefix . '_source';
    $xartable['helpdesk_priority'] = $coprefix . '_priority';
    
    // Return table information
    return $xartable;
}
?>
