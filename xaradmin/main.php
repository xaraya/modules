<?php
/**
 * File: $Id$
 *
 * Admin Main
 *
 * @package authentication
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage authinvision2
 * @author Brian McCloskey <brian@nexusden.com>
*/
function authinvision2_admin_main()
{
    // Security check
    if(!xarSecurityCheck('Adminauthinvision2')) return;

    // return array from admin-main template
    return array();
}
?>
