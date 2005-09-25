<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * the main administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments.
 */
function sitetools_admin_main()
{ 
    /* Security check */
    if (!xarSecurityCheck('EditSiteTools')) return;
    /* The admin system looks for a var to be set to skip the introduction
     * page altogether.  This allows you to add sparse documentation about the
     * module, and allow the site admins to turn it on and off as they see fit.
     */
    if (xarModGetVar('adminpanels', 'overview') == 0) {

        $data = xarModAPIFunc('sitetools', 'admin', 'menu');
        $data['welcome'] = '';
        return $data;

    } else {
        xarResponseRedirect(xarModURL('sitetools', 'admin', 'modifyconfig'));
    }
  
    return true;
}
?>