<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/

/**
 * the main administration function
 *
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return bool true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function keywords_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminKeywords')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>