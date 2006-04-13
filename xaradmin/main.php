<?php
/**
 * Change Log Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * the main administration function
 * Redirect to modifyconfig
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return bool true on success of redirect or void on failure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function changelog_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    xarResponseRedirect(xarModURL('changelog', 'admin', 'view'));
    // success
    return array(); //true;
}

?>
