<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 * The main helpdesk administration function
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments. It redirects the admin to the view function.
 *
 * @return bool true on success of redirect
 */
function helpdesk_admin_main()
{
    if( !Security::check(SECURITY_ADMIN, 'helpdesk') ){ return false; }


    xarResponseRedirect(xarModURL('helpdesk', 'admin', 'view', array('itemtype' => 1)));
    return true;

}
?>
