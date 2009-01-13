<?php
/**
 * Julian main administration function
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * the main administration function
 *
 * This function forwards to the Modify Config page or to the Overview page 
 *
 * @author MichelV <michelv@xaraya.com>
 * @return true
 */
function julian_admin_main()
{
    if (xarSecurityCheck('AdminJulian')) {
        xarResponseRedirect(xarModURL('julian', 'admin', 'modifyconfig'));
        return true;
    }
    // Fallback with low security mask for using the overview page as 
    // inital help page for editors 
    if (xarSecurityCheck('EditJulian'))  {
        xarResponseRedirect(xarModURL('julian', 'admin', 'overview'));
    }
    
    return true;
}
?>