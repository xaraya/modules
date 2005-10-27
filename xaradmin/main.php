<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Add a standard screen upon entry to the module.
 *
 * @public
 * @author John Cox 
 * @returns output
 * @return output with censor Menu information
 */
function html_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditHTML')) return;

    // Get the admin menu
    $data = xarModAPIFunc('html', 'admin', 'menu');

    // Return the template variables defined in this function
    return $data;
}

?>
