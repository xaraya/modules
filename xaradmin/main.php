<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Show main administration page
 */
function shouter_admin_main()
{

    if (!xarSecurityCheck('EditShouter')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('shouter', 'admin', 'menu');

        return $data;
    } else {
        xarResponseRedirect(xarModURL('shouter', 'admin', 'view'));
    }

    return true;
}
?>