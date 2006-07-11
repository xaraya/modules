<?php
/**
 * AuthURL Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthURL
 * @link http://xaraya.com/index.php/release/42241.html
 * @author Court Shrock <shrockc@inhs.org>
 */

/**
 * the main administration function
 */
function authurl_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthURL')) return;

    // return array from admin-main template
    return array();
}

?>
