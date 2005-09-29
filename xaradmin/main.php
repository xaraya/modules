<?php
/**
 * The main administration function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @package Sitecontact
 */

/**
 * The main administration function
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function sitecontact_admin_main()
{
    if (!xarSecurityCheck('EditSiteContact')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('sitecontact', 'admin', 'menu');
        /* Specify some other variables used in the blocklayout template */
        $data['welcome'] = xarML('Welcome to the administration part of this SiteContact module...');

        /* Return the template variables defined in this function */
        return $data;
    } else {
        xarResponseRedirect(xarModURL('sitecontact', 'admin', 'modifyconfig'));
    }
    /* success */
    return true;
}
?>