<?php
/**
 * Shows the privacy policy if set as a modvar
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * Shows the privacy policy if set as a modvar
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
 */
function registration_user_privacy()
{
    // Security check
    if (!xarSecurityCheck('ViewRegistration')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Privacy Statement')));
    return array();
}
?>