<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
 * @return array $data
 */
function newsletter_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin edit menu
    $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // See if user is logged in
    if (xarUserIsLoggedIn()) {
        $data['logged'] = true;
    } else {
        $data['logged'] = false;
    }

    // Return the template variables defined in this function
    return $data;
}
?>
