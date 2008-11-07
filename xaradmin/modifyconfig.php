<?php
/**
 * Scheduler module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Scheduler Module
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function scheduler_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    $data = array();

    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $data['proxy'] = $data['ip'];
        $data['ip'] = preg_replace('/,.*/', '', $forwarded);
        $data['ip'] = xarVarPrepForDisplay($data['ip']);
    }

    $data['jobs'] = xarModAPIFunc('scheduler','user','getall');

    $modules = xarModAPIFunc('modules', 'admin', 'getlist',
                             array('filter' => array('AdminCapable' => true)));
    $data['modules'] = array();
    foreach ($modules as $module) {
        $data['modules'][$module['name']] = $module['displayname'];
    }
    $data['types'] = array( // don't translate API types
                           'scheduler' => 'scheduler',
                           'admin' => 'admin',
                           'user' => 'user',
                          );
    $data['intervals'] = xarModAPIFunc('scheduler','user','intervals');
    $data['triggers'] = xarModAPIFunc('scheduler','user','triggers');
    

    $hooks = xarModCallHooks('module', 'modifyconfig', 'scheduler',
                             array('module' => 'scheduler'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}
?>
