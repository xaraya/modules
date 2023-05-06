<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * the main administration function
 * This function will show the overview page with information on this module.
 *
 * @param none
 * @return array
 */
function logconfig_admin_overview()
{
    if (!xarSecurity::check('AdminLogConfig')) return;


    return xarTpl::module('logconfig', 'admin', 'main', $data,'main');

    // Return the template variables defined in this function
    return $data;
}

?>