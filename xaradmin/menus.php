<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function menutree_admin_menus()
{
    // Check to see the current user has edit access to the menutree module
    if (!xarSecurityCheck('EditMenuTree')) return;

	if (!xarVarFetch('parentid',        'int', $data['parentid'],       '0', XARVAR_NOT_REQUIRED)) return;
    
    return xarTplModule('menutree','admin','menus',$data);
}

?>