<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

/**
 * ItemDelete Hook Subject Observer
 *
**/
sys::import('modules.pubsub.class.hookobservers.base');
class PubsubItemDeleteObserver extends PubsubBaseObserver implements ixarEventObserver
{
    public function notify(ixarEventSubject $subject)
    {
        // Get extrainfo from subject (array containing object_id, module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();

        try {
            $valid_array = $this->validate($extrainfo);
            // If validation failed, just return to sender
            if (!$valid_array) {
                return $extrainfo;
            }
            // Validation succeeded; take the result
            $extrainfo = $valid_array;
        } catch (Exception $e) {
            // Something went wrong
            throw $e;
        }

        // Get information about the template we will use
        $template_id = $this->getTemplate($extrainfo);

        // Process the event (i.e. create a job for each event)
        xarMod::apiFunc(
            'pubsub',
            'admin',
            'processevent',
            array('module_id'   => $extrainfo['module_id'],
                             'itemtype'    => $extrainfo['itemtype'],
                             'cid'         => $extrainfo['cid'],
                             'itemid'      => $extrainfo['itemid'],
//                             'extra'       => $extra,
                             'object_id'   => $extrainfo['object_id'],
                             'template_id' => $template_id,
                             'event_type'  => 'itemdelete',
                             'url'         => $extrainfo['url'],
                             'state'       => 2
                             )
        );
                         
        // The subject expects an array of extrainfo: whether or not the event was created, we go on.
        return $extrainfo;
    }
}
