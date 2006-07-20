<?php
/**
 * Sniffer System
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
