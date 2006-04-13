<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
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
    $coprefix = xarDBGetSiteTablePrefix() . '_helpdesk';

    $xartable['helpdesk_tickets'] = $coprefix.'_tickets';
    $xartable['helpdesk_status'] = $coprefix . '_status';
    $xartable['helpdesk_source'] = $coprefix . '_source';
    $xartable['helpdesk_priority'] = $coprefix . '_priority';

    // Return table information
    return $xartable;
}
?>
