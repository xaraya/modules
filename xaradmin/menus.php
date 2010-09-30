<?php
/**
 * The admin menutree
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/eid/1162
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  
 */
function menutree_admin_menus() {
    // Check to see the current user has edit access to the menutree module
    if (!xarSecurityCheck('EditMenuTree')) return;

	if (!xarVarFetch('parentid',        'int', $data['parentid'],       '0', XARVAR_NOT_REQUIRED)) return;
    
    return xarTplModule('menutree','admin','menus',$data);
}

?>