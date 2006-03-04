<?php
/**
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel Module Development Team
 */
/**
 * The main administration function
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @return bool true on success of redirect
 */
function sigmapersonnel_admin_main()
{

    if (!xarSecurityCheck('EditSIGMAPersonnel')) return;
    xarResponseRedirect(xarModURL('sigmapersonnel', 'admin', 'view'));
    /* success so return true */
    return true;
}
?>