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
function menutree_admin_test()
{

	print 'TEST99'; exit;
    // Check to see the current user has edit access to the menutree module
    if (!xarSecurityCheck('EditMenuTree')) return;
    
    return xarTplModule('menutree','admin','foldermenutree');
}

?>