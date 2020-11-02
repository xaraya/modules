<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * search for scheduler API functions in modules/<module>/xarschedulerapi directories
 */
function scheduler_admin_search()
{
    if (!xarSecurity::check('AdminScheduler')) {
        return;
    }

    $data = array();
    $data['found'] = array();

    $items = xarMod::apiFunc('modules', 'admin', 'getlist', array('filter' => array('State' => xarMod::STATE_ACTIVE)));
    $activemodules = array();
    foreach ($items as $item) {
        $activemodules[$item['name']] = 1;
    }

    $modules = sys::code().'modules';
    $dh = opendir($modules);
    if (empty($dh)) {
        return $data;
    }
    while (($dir = readdir($dh)) !== false) {
        if (is_dir($modules . '/' . $dir) && is_dir($modules . '/' . $dir . '/xarschedulerapi')) {
            if (!isset($activemodules[$dir])) {
                continue;
            }
            $dh2 = opendir($modules . '/' . $dir . '/xarschedulerapi');
            if (empty($dh2)) {
                continue;
            }
            while (($file = readdir($dh2)) !== false) {
                if (preg_match('/^(\w+)\.php$/', $file, $matches)) {
                    $data['found'][] = array('module' => $dir, // not really, but let's not be difficult
                                             'type' => 'scheduler',
                                             'func' => $matches[1]);
                }
            }
            closedir($dh2);
        }
    }
    closedir($dh);
    return $data;
}
