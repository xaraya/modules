<?php
/**
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 */
/**
 * the main administration function
 */
function julian_admin_main()
{

// Security Check
    if (!xarSecurityCheck('Editjulian')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        $welcome = '';

        // Return the template variables defined in this function
        return array('welcome' => $welcome);
    } else {
        xarResponseRedirect(xarModURL('julian', 'admin', 'modifyconfig'));
    }
    // success
    return true;

}

?>