<?php
/**
 * formantibot API
 *
 * @package Modules
 * @copyright (C) 2002-2006 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage formantibot
 * @link http://xaraya.com/index.php/release/761.html 
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/


/**
 * Displays the overview menu if adminpanels.overview is
 * turned on otherwise, it displays the formantibot editing page
 *
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @returns mixed output array, or string containing formated output
 */
function formantibot_admin_main()
{
    if(!xarSecurityCheck('FormAntiBot-Admin')){
        return;
    }

    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0){
        return array();
    } else {
        xarResponseRedirect(xarModURL('formantibot', 'admin', 'view'));
    }
    // success
    return true;
}
?>
