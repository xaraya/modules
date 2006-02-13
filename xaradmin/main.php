<?php
/**
 * The main administration function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * The main administration function
 *
 * @author jojodee
 */
function legis_admin_main()
{ 
    if (!xarSecurityCheck('EditLegis')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {

        $data = xarModAPIFunc('legis', 'admin', 'menu');

        return $data;
    } else {
        /* If the Overview documentation is turned off, then we just return the view page,
         * or whatever function seems to be the most fitting.
         */
        xarResponseRedirect(xarModURL('legis', 'admin', 'view'));
    }
    /* success so return true */
    return true;
}
?>
