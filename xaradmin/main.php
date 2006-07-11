<?php
/**
 * AuthSSO Administrative Display Functions
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSSO
 * @link http://xaraya.com/index.php/release/51.html
 * @author Jonn Beames <jsb@xaraya.com> | Richard Cave <rcave@xaraya.com>
*/

/**
 * the main administration function
 */
function authsso_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthSSO')) return;

    // return array from admin-main template
    return array();
}

?>