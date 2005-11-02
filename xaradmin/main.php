<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage Wizards Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  Function decides if user is logged in
 * and returns user to correct location.
 *
*/
function wizards_admin_main()
{

// Security Check
    if(xarSecurityCheck('EditWizard',0)) {

        if (xarModGetVar('adminpanels', 'overview') == 0){
            return xarTplModule('wizards','admin', 'main',array());
        } else {
                 xarResponseRedirect(xarModURL('wizards', 'admin', 'listscripts',
                                                array('info' => xarRequestGetInfo())));
        }
    }
    else { return; }
}

?>