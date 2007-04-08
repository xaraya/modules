<?php
/**
 * Webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * Update configuration
 */
function webshare_admin_updateconfig()
{
    // Get parameters
    if(!xarVarFetch('active', 'array', $active, array(), XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminWebshare')) return;

    if (!xarModAPIFunc('webshare','admin','update',array('active'=>$active))) {
	   return;
	}

    xarResponseRedirect(xarModURL('webshare', 'admin', 'modifyconfig'));

    return true;
}

?>
