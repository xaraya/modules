<?php
/**
 * Tasks module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tasks Module Development Team
 */
/**
 * Administration entry point
 * @author Chad Kraeft
 */
function tasks_admin_main()
{
    $data=array();
    $data['welcome']=xarML('Welcome to the administration part of tasks module...');
    $data['pageinfo']=xarML('Overview');
    return $data;
}
?>