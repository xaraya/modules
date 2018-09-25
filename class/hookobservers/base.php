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
class PubsubBaseObserver extends HookObserver implements ixarEventObserver
{
    public $module = 'pubsub';

    public function getTemplate($extrainfo)
    {
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
            return false;
        }
        return $template_id;
    }
}

?>