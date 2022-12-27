<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function calendar_adminapi_hookupdate($data)
{
    if (!isset($data['extrainfo']) || !is_array($data['extrainfo'])) {
        $data['extrainfo'] = [];
    }

    // When called via hooks, modname will be empty, but we get it from the
    // extrainfo or the current module
    if (empty($data['module'])) {
        if (!empty($data['extrainfo']['module'])) {
            $data['module'] = $data['extrainfo']['module'];
        } else {
            $data['module'] = xarMod::getName();
        }
    }
    $data['module_id'] = xarMod::getID(($data['module']));
    if (empty($data['module_id'])) {
        throw new IDNotFoundException("module id for " . $data['modname']);
    }

    if (!isset($data['itemtype']) || !is_numeric($data['itemtype'])) {
        if (isset($data['extrainfo']['itemtype']) && is_numeric($data['extrainfo']['itemtype'])) {
            $data['itemtype'] = $data['extrainfo']['itemtype'];
        } else {
            $data['itemtype'] = 0;
        }
    }
    if (!isset($data['itemid']) || !is_numeric($data['itemid'])) {
        if (isset($data['extrainfo']['item_id']) && is_numeric($data['extrainfo']['item_id'])) {
            $data['itemid'] = $data['extrainfo']['item_id'];
        } else {
            $data['itemid'] = 0;
        }
    }

    $data['extrainfo']['module_id'] = $data['module_id'];
    $data['extrainfo']['itemtype'] = $data['itemtype'];
    $data['extrainfo']['item_id'] = $data['itemid'];

    $data['extrainfo']['name'] ??= xarML('Unknown Event');
    $data['extrainfo']['start_time'] ??= time();
    $data['extrainfo']['duration'] ??= 60;
    $data['extrainfo']['end_time'] ??= $data['extrainfo']['start_time'] + $data['extrainfo']['duration'];
    $data['extrainfo']['recurring_code'] ??= 0;
    $data['extrainfo']['recurring_span'] ??= 0;

    $data['extrainfo']['start_location'] ??= null;
    $data['extrainfo']['end_location'] ??= null;
    $data['extrainfo']['object_id'] ??= 0;
    $data['extrainfo']['role_id'] ??= xarSession::getVar('role_id');
    $data['extrainfo']['return_link'] ??= '';
    $data['extrainfo']['state'] ??= 3;
    $data['extrainfo']['timestamp'] ??= time();

    $object = DataObjectMaster::getObject(['name' => 'calendar_event']);
    $item = $object->updateItem($data['extrainfo']);

    return $data['extrainfo'];
}
