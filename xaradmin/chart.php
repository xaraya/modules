<?php
/**
 * File: $Id$
 *
 * Sniffer Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Sniffer Module
 * @author Frank Besler
 *
 * Using phpSniffer by Roger Raymond
 * Purpose of file: find out the browser and OS of the visitor
*/

/**
 * Utility function to show a pie chart
 *  
 * Based on work by:
 * 2D Pie Chart Version 1.0
 * Programer: Xiao Bin Zhao
 * E-mail: love1001_98@yahoo.com
 * Date: 03/31/2001
 * All Rights Reserved 2001.
 *
 * @author Richard Cave
 * @param nada 
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sniffer_admin_chart()
{
    // Get the user menu
    $data = xarModAPIFunc('sniffer', 'admin', 'menu');

    return $data;
}

?>
