<?php
/**
 * Xaraya BBCode
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
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
function bbcode_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditBBCode')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('bbcode', 'admin', 'modifyconfig'));
    } 
    // success
    return true;
}
?>