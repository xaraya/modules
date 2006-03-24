<?php
/*
 * Main
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * the main administration function
 * 
 * @author jsb | mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function xarcachemanager_admin_main()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminXarCache')) return;

        xarResponseRedirect(xarModURL('xarcachemanager', 'admin', 'modifyconfig'));
    // success
    return true;
} 

?>