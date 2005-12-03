<?php
/**
* main admin function
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage bible
* @link http://xaraya.com/index.php/release/550.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* main administration function
*/
function bible_admin_main()
{
    // security check
    if (!xarSecurityCheck('AdminBible')) return;

    // show overview or redirect to a more useful function
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return xarModAPIFunc('bible', 'admin', 'menu');
    } else {
        xarResponseRedirect(xarModURL('bible', 'admin', 'view'));
    }

    // success
    return true;
}

?>
