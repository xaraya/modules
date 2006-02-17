<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * the main administration function
 */
function logconfig_admin_main()
{
    if (!xarSecurityCheck('AdminLogConfig')) return;

    if (!(xarModGetVar('adminpanels', 'overview') == 0)){
        xarResponseRedirect(xarModURL('logconfig', 'admin', 'view'));
    }

    $data = xarModAPIFunc('logconfig','admin','menu');

    // Return the template variables defined in this function
    return $data;
}

?>