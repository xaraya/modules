<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with Autolinks Menu information
 */
function hitcount_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminHitcount')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('hitcount', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

?>