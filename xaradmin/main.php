<?php
/**
 * Maxercalls main administration function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * the main administration function
 */
function maxercalls_admin_main()
{
    // Security check
    if (!xarSecurityCheck('EditMaxercalls')) return;
    // The admin system looks for a var to be set to skip the introduction
    // page altogether.
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        // If you want to go directly to some default function, instead of
        // having a separate main function, you can simply call it here, and
        // use the same template for admin-main.xard as for admin-view.xard
        return xarModFunc('maxercalls','admin','viewcalls');
        $data = xarModAPIFunc('maxercalls', 'admin', 'menu');
        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the administration part of the Maxercalls module...');
        // Return the template variables defined in this function
        return $data;
    } else {
        // If docs are turned off, then we just return the view page, or whatever
        // function seems to be the most fitting.
        xarResponseRedirect(xarModURL('maxercalls', 'admin', 'viewcalls'));
    }
    // success
    return true;
}
?>