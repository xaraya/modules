<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 */
/**
 * Event Subject Observer
 *
 * Event Subject is notified every time xarEvents::notify is called
 * see /code/modules/eventsystem/class/eventsubjects/event.php for subject info 
 *
 * This observer is responsible for logging the event to the system log
**/
sys::import('xaraya.structures.hooks.observer');
class HitcountItemDisplayObserver extends EventObserver implements ixarEventObserver
{
    public $module = 'hitcount';
    public function notify(ixarEventSubject $subject)
    {
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();
        extract($extrainfo);

        // validate parameters...
        // NOTE: this isn't strictly necessary, the hook subject will have already
        // taken care of validations and these values can be relied on to be pre-populated
        // however, just for completeness...        
        if (!isset($module) || !is_string($module) || !xarMod::isAvailable($module))
            $invalid['module'] = 1; 
        if (isset($itemtype) && !is_numeric($itemtype))
            $invalid['itemtype'] = 1;
        if (!isset($itemid) || !is_numeric($itemid))
            $invalid['itemid'] = 1;
        
        // NOTE: as of Jamaica 2.2.0 it's ok to throw exceptions in hooks, the subject handles them
        if (!empty($invalid)) {
            $args = array(join(',',$invalid), 'hitcount', 'hooks', 'ItemDelete');
            $msg = 'Invalid #(1) for #(2) module #(2) #(3) observer notify method';
            throw new BadParameterException($args, $msg);
        }
        
        // the subject expects a string to display, return the display gui func
        return xarMod::guiFunc('hitcount', 'user', 'display', 
            array(
                'modname' => $module,
                'itemtype' => !empty($itemtype) ? $itemtype : 0,
                'objectid' => $itemid,
            ));
    }
}
?>