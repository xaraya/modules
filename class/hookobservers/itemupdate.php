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
    public $module = 'pubsub';
    public function notify(ixarEventSubject $subject)
    {
        // get extrainfo from subject (array containing module, module_id, itemtype, itemid)
        $extrainfo = $subject->getExtrainfo();

        try {
            $valid_array = $this->validate($extrainfo);
            // If validation failed, just return to sender
            if (!$valid_array) return $extrainfo;
            // Validation succeeded; take the result
            $extrainfo = $valid_array;
        } catch (Exception $e) {
            // Something went wrong
            throw $e;
        }

        $typeoftemplate = 'update';
        
        sys::import('modules.dynamicdata.class.properties.master');
        $templates = DataObjectMaster::getObjectList(array('name' => 'pubsub_templates'));
        $q = $templates->dataquery;
        if (!empty($extrainfo['object'])) {
            $q->eq('object_id', $extrainfo['object_id']);
        } elseif (!empty($extrainfo['module_id'])) {
            $q->eq('module_id', $extrainfo['module_id']);
            $q->eq('itemtype', $extrainfo['itemtype']);
        }
        $q->addfield('id');
        $q->run();
        $result = $q->output();
        if (!empty($result)) {
            // Use the template found
            $row = reset($result);
            $template_id = (int)$row['id'];
        } elseif (empty($result) && xarModVars::get('pubsub', 'enable_default_template')) {
            // Use the default template
            $template_id = 1;
        } else {
            // We have no template: bail
            return $extrainfo;
        }

/*
    $extra = null;
// FIXME: handle 2nd-level hook calls in a cleaner way - cfr. categories navigation, comments add etc.
    if ($modname == 'comments') {
        $extra = '';
        if (isset($extrainfo['current_module']) && is_string($extrainfo['current_module'])) {
            $extra = xarModGetIDFromName($extrainfo['current_module']);
        }
        if(isset($extrainfo['current_itemtype']) && is_numeric($extrainfo['current_itemtype'])) {
            $extra .= '-' . $extrainfo['current_itemtype'];
        }
        if(isset($extrainfo['current_itemid']) && is_numeric($extrainfo['current_itemid'])) {
            $extra .= '-' . $extrainfo['current_itemid'];
        }
    }
*/
    // Process the event (i.e. create a job for each subscription)
    xarMod::apiFunc('pubsub','admin','processevent',
                       array('module'   => $extrainfo['module_id'],
                             'itemtype'    => $extrainfo['itemtype'],
                             'cid'         => $extrainfo['cid'],
                             'itemid'      => $extrainfo['itemid'],
//                             'extra'       => $extra,
                             'object'   => $extrainfo['object_id'],
                             'template_id' => $template_id));
                         
        // The subject expects an array of extrainfo: whether or not the event was created, we go on.
        return $extrainfo;
    }
}

?>