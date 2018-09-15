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
 * ItemUpdate Hook Subject Observer
 *
**/
sys::import('xaraya.structures.hooks.observer');
class PubsubItemUpdateObserver extends HookObserver implements ixarEventObserver
{
    public $module = 'eventhub';
    public function notify(ixarEventSubject $subject)
    {
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();
            
        // the subject expects an array of extrainfo
        // return the array of extrainfo
        return $extrainfo;
    }
}

?>