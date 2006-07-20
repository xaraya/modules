<?php
/**
 * Sniffer System - find out the browser and OS of the visitor
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
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
