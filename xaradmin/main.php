<?php
/**
 * Main admin function
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
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * the main registration function
 */
function registration_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditRegistration')) return;

    xarResponseRedirect(xarModURL('registration', 'admin', 'modifyconfig'));
    // success
    return true;
}
?>