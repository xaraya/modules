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
 * get information about all scheduler jobs
 *
 * @author mikespub
 * @param string $args['module']: module name +
 * @param string $args['type']: api type +
 * @param string $args['function']: function name, or
 * @param int    $args['trigger']: 0: disabled, 1: external, 2: block, 3: event
 * @return array of jobs and their info
 */
function scheduler_userapi_getall($args)
{
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObjectList(array('name' => 'scheduler_jobs'));
    
    // We want to get all the fields
    foreach ($object->properties as $key => $value) {
        if ($value->getDisplayStatus() == DataPropertyMaster::DD_DISPLAYSTATE_DISABLED) continue;
        $object->properties[$key]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
    }
    if (isset($args['trigger'])) $object->dataquery->eq('job_trigger', $args['trigger']);
    $items = $object->getItems();

    return $items;
}

?>
