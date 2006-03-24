<?php
/**
 * Shows the user terms if set as a modvar
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
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