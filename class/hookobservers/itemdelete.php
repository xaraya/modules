<?php
/**
 * Eventhub Module
 *
 * @package modules
 * @subpackage eventhub module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
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
class PubsubItemDeleteObserver extends HookObserver implements ixarEventObserver
{
    public $module = 'eventhub';
    public function notify(ixarEventSubject $subject)
    {
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();
        return $extrainfo;

    }
}
?>