<?php
/**
 * Shows the user terms if set as a modvar
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 */
/**
 * Shows the user terms if set as a modvar
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 */
function registration_user_terms()
{
    // Security check
    if (!xarSecurityCheck('ViewRegistration')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Terms of Usage')));
    return array();
}
?>