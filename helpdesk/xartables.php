<?php
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

// ----------------------------------------------------------------------
// helpdesk - helpdesk module
// Copyright (C) 2003 By Brian McGilligan, Pensacola, Florida.
// bmcgilligan@abrasiontechnology.com
// http://www.abrasiontechnology.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Modified by: Brian McGilligan
// --------+--------------------------------------------------------------

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
    // Name for dq_helpdesk database entities
    $coprefix = xarConfigGetVar('prefix') . '_helpdesk';

    $xartable['helpdesk_tickets'] = $coprefix.'_tickets';
    $xartable['helpdesk_tickets_column'] = array (
                'ticket_id'         => "xar_id",
                'ticket_date'       => "xar_date",
                'ticket_statusid'   => "xar_statusid",
                'ticket_priorityid' => "xar_priorityid",
                'ticket_sourceid'   => "xar_sourceid",
                'ticket_openedby'   => "xar_openedby",
                'ticket_assignedto' => "xar_assignedto",
                'ticket_closedby'   => "xar_closedby",
                'ticket_subject'    => "xar_subject",
                'ticket_domain'     => "xar_domain",
                'ticket_lastupdate' => "xar_updated"
                );
                
    $xartable['helpdesk_status'] = $coprefix . '_status';
    $xartable['helpdesk_source'] = $coprefix . '_source';
    $xartable['helpdesk_priority'] = $coprefix . '_priority';
    
    // Return table information
    return $xartable;
}
?>
