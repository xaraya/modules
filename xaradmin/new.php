<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * Modify extra information for scheduler jobs
 * @param id itemid
 */
function scheduler_admin_new()
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    // Use the current job as $data
    $data = array();

    $modules = xarModAPIFunc('modules', 'admin', 'getlist',
                             array('filter' => array('AdminCapable' => 1)));
    $data['modules'] = array();
    foreach ($modules as $module) {
        $data['modules'][$module['name']] = $module['displayname'];
    }
    $data['types'] = array( // don't translate API types
                           'scheduler' => 'scheduler',
                           'admin' => 'admin',
                           'user' => 'user',
                          );

    $data['triggers'] = xarModAPIFunc('scheduler','user','triggers');
    $data['sources'] = xarModAPIFunc('scheduler','user','sources');

    $data['authid'] = xarSecGenAuthKey();
    $data['intervals'] = xarModAPIFunc('scheduler','user','intervals');

    // Return the template variables defined in this function
    return $data;
}
?>
