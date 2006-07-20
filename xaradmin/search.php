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
 * search for scheduler API functions in modules/<module>/xarschedulerapi directories
 */
function scheduler_admin_search()
{
    if (!xarSecurityCheck('AdminScheduler')) return;

    $data = array();
    $data['found'] = array();

    $modules = realpath('modules');
    $dh = opendir($modules);
    if (empty($dh)) return $data;
    while (($dir = readdir($dh)) !== false) {
        if (is_dir($modules . '/' . $dir) && is_dir($modules . '/' . $dir . '/xarschedulerapi')) {
            $dh2 = opendir($modules . '/' . $dir . '/xarschedulerapi');
            if (empty($dh2)) continue;
            while (($file = readdir($dh2)) !== false) {
                if (preg_match('/^(\w+)\.php$/',$file,$matches)) {
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
?>