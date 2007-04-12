<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * Update configuration
 */
function sharecontent_admin_updatewebconfig()
{
    // Get parameters
    if(!xarVarFetch('active', 'array', $active, array(), XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminSharecontent')) return;

    if (!xarModAPIFunc('sharecontent','admin','update',array('active'=>$active))) {
	   return;
	}

    xarResponseRedirect(xarModURL('sharecontent', 'admin', 'webconfig'));

    return true;
}

?>
