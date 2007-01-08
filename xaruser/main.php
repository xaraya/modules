<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.
 * We will redirect to the ITSP of the user if that exists, or redirect to the view function
 * @author the ITSP module development team
 */
function itsp_user_main()
{
    if (!xarSecurityCheck('ViewITSP')) return;

    // See if user has a registered ITSP
    $uid = xarUserGetVar('uid');
    $itsp = xarModApiFunc('itsp','user','get_itspid',array('userid'=>$uid));
    // Need to set the func
    // Move on to ITSP when one is found
    if (!empty($itsp)) {
        return xarModFunc('itsp','user','itsp');
    } else {
        return xarModFunc('itsp','user','view');
    }
}
?>