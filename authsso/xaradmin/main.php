<?php
/**
 * File: $Id$
 *
 * AuthSSO Administrative Display Functions
 * 
 * @package authentication
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authsso
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
