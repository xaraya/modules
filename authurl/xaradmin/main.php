<?php
/**
 * File: $Id$
 *
 * AuthURL Administrative Display Functions
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authurl
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
