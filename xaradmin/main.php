<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * The main administration function
 *
 * This function is the default function, and is called whenever the
 * module is initiated without defining arguments. As such it can
 * be used for a number of things, but most commonly it either just
 * shows the module menu and returns or calls whatever the module
 * designer feels should be the default function (often this is the
 * view() function)
 *
 * @author Example Module Development Team
 * @access public
 * @return Specify your return type here
 */
function example_admin_main()
{
    /* Security check -  For the
     * main function we check that the user has at least Edit privilege
     * for some item within this component, or else they won't be able to do
     * anything and so we refuse access altogether. The lowest level of access
     * for administration depends on the particular module, but it is generally
     * either 'edit' or 'add'
     */
    if (!xarSecurityCheck('EditExample')) return;
       /* If you want to go directly to some default function, instead of
         * having a separate main function, you can simply call it here, and
         * use the same template for admin-main.xd as for admin-view.xd:
         * 
         * $data = xarModFunc('example','admin','view');
         * return $data;
         */

        /* You could specify some other variables to use in the blocklayout template
         * $data['welcome'] = xarML('Welcome to the administration part of this Example module...');
         * Return the template variables defined in this function
         */
        
        /* If no main function as such, redirect to the view page,
         * or whatever function seems to be the most fitting for this module.
         */
        xarResponseRedirect(xarModURL('example', 'admin', 'view'));

    /* success so return true */
    return true;
}
?>