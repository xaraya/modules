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
 * Add a standard screen upon entry to the module.
 *
 * @public
 * @author Richard Cave
 * @return output with censor Menu information
 */
function sniffer_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminSniffer')) return;

    // Get the admin menu
    $data = xarModAPIFunc('sniffer', 'admin', 'menu');

    // Return the template variables defined in this function
    return $data;
}

?>
