<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * the main administration function
 *
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_configdesc()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    if (!xarVarFetch('func', 'str', $data['page'],  'main', XARVAR_NOT_REQUIRED)) return;

    // Get the admin edit menu
    $data['menu'] = xarModApiFunc('newsletter', 'admin', 'configmenu');

    // Return the template variables defined in this function
    return $data;
}

?>
