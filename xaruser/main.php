<?php
/**
 * Main user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
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
function wizards_user_main()
{

// Security Check
    if(xarSecurityCheck('ViewWizards',0)) {
         xarResponseRedirect(xarModURL('wizards', 'user', 'listscripts',
                                        array('info' => xarRequestGetInfo())));
    }
    else { return; }

}

?>