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
 * get information about a scheduler job
 *
 * @author mikespub
 * @param  $args ['module'] module +
 * @param  $args ['functype'] type +
 * @param  $args ['func'] API function, or
 * @param  $args ['itemid'] job id
 * @return array of job info on success, void on failure
 */
function scheduler_userapi_get($args)
{
    extract($args);
    if ((empty($itemid) || !is_numeric($itemid)) && (empty($module) || !is_string($module)) && (empty($type) || !is_string($type)) && (empty($func) || !is_string($func))) {
        throw new Exception(xarML('No itemid or URL parameters passed'));
    }

    sys::import('modules.dynamicdata.class.objects.master');
    if (!empty($itemid)) {
        $object = DataObjectMaster::getObject(['name' => 'scheduler_jobs']);
        $object->getItem(['itemid' => $args['itemid']]);
        $job = $object->getFieldValues();
    } else {
        $object = DataObjectMaster::getObjectList(['name' => 'scheduler_jobs']);
        $object->dataquery->eq('module', $module);
        $object->dataquery->eq('type', $type);
        $object->dataquery->eq('function', $func);
        $items = $object->getItems();
        if (empty($items)) {
            return $items;
        }
        $job = current($items);
    }

    return $job;
}
