<?php 

/**
 * Locator Utilities
 *
 * @copyright   by Michael Cortez
 * @license     GPL (http://www.gnu.org/licenses/gpl.html)
 * @author      Michael Cortez
 * @link        
 *
 * @package     Xaraya eXtensible Management System
 * @subpackage  HookBridge Utility
 * @version     $Id$
 *
 */

/*
 * The main ( default ) administration view.
 */

function hookbridge_admin_main() 
{

    if (!xarSecurityCheck( 'Adminhookbridge')) return;

    // Check if we should show the overview page 
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.  This allows you to add sparse documentation about the
    // module, and allow the site admins to turn it on and off as they see fit. 
    if (xarModGetVar('adminpanels', 'overview') == 0) {

        // Yes we should
        $data = xarModAPIFunc(
            'hookbridge'
            ,'private'
            ,'common'
            ,array(
                'title' => xarML( 'Overview' )
                ,'type' => 'admin'
                ));
        return $data;
    }

    // No we shouldn't. So we redirect to the admin_view() function.
    return xarResponseRedirect(
        xarModURL(
            'hookbridge'
            ,'admin'
            ,'view' ));

}

/*
 * END OF FILE
 */
?>
