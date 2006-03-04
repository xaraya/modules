<?php
/**
* Main administration function
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Main administration function
*
* Show overview or go to a more useful function.
*/
function ebulletin_admin_main()
{
    // security check
    if (!xarSecurityCheck('AdmineBulletin')) return;

    // show overview or redirect to a more useful function
   // if (xarModGetVar('adminpanels', 'overview') == 0) {
   //     return xarModAPIFunc('ebulletin', 'admin', 'menu');
   // } else {
        xarResponseRedirect(xarModURL('ebulletin', 'admin', 'view'));
   // }

    // success
    return true;
}

?>
