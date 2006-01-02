<?php
/**
 * Shows the privacy policy if set as a modvar
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 */
/**
 * Shows the privacy policy if set as a modvar
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 */
function authentication_user_privacy()
{
    // Security check
    if (!xarSecurityCheck('ViewAuthentication')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Privacy Statement')));
    return array();
}
?>