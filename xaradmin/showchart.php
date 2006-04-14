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
 * @public
 * @author Richard Cave
 * @param nada 
 * @return array of items, or false on failure
 * @raise FORBIDDEN_OPERATION
 */
function sniffer_admin_showchart()
{
    // Check that the GD library is available
    if (!extension_loaded('gd')) {
        $msg = xarML('The GD graphics library is required to chart.');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION',
                       new SystemException($msg));
        return;
    }

    // Get parameters from the input
    if (!xarVarFetch('snifftype', 'str:1:', $type, 'osnam')) return;

    switch($type) {
        case 'osnam':
            $title = "OS Name";
            break;
        case 'osver':
            $title = "OS Version";
            break;
        case 'agnam':
            $title = "Browser Name";
            break;
        default:
            $title = 'Sniffer Results';
            break;
    }

    xarModAPIFunc('sniffer',
                  'admin',
                  'drawchart',
                  array('type' => $type,
                        'title' => $title));

    // Don't return to the template!!!
    exit();
}

?>
