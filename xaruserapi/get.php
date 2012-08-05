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
    $invalid = array();
    if (empty($args['itemid']) || !is_numeric($args['itemid'])) {
        throw new Exception(xarML('No itemid passed'));
    }
    /* Just focus on itemid for now
        if (empty($module) || !is_string($module)) {
            $invalid[] = 'module';
        }
        // CHECKME: why can't we use type instead of functype here?
        if ((empty($type) || !is_string($type)) && (empty($type) || !is_string($type))) {
            $invalid[] = 'type';
        }
        if (empty($function) || !is_string($function)) {
            $invalid[] = 'function';
        }
        if (count($invalid) > 0) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         join(', ', $invalid), 'user', 'get', 'scheduler');
            throw new BadParameterException($msg);
        }

    */
    
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'scheduler_jobs'));
    $object->getItem(array('itemid' => $args['itemid']));
    $job = $object->getFieldValues();

    return $job;
}

?>