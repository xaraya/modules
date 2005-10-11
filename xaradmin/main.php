<?php
/**
 * Xaraya POP3 Gateway
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage pop3gateway Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author John Cox
 */

/**
 * Manually import emails
 * 
 * @author John Cox
 */
function pop3gateway_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminPOP3Gateway')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('pop3gateway', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>
