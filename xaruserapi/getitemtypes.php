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

function calendar_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[1] = array('label' => xarML('Event'),
                          'title' => xarML('View Event'),
                          'url'   => xarController::URL('calendar','user','view')
                         );
    $itemtypes[2] = array('label' => xarML('ToDo'),
                          'title' => xarML('View ToDo'),
                          'url'   => xarController::URL('calendar','user','view')
                         );
    $itemtypes[3] = array('label' => xarML('Alarm'),
                          'title' => xarML('View Alarm'),
                          'url'   => xarController::URL('calendar','user','view')
                         );
    $itemtypes[4] = array('label' => xarML('FreeBusy'),
                          'title' => xarML('View FreeBusy'),
                          'url'   => xarController::URL('calendar','user','view')
                         );
    // @todo let's use DataObjectMaster::getModuleItemType here, but not until roles brings in dd automatically
    $extensionitemtypes = xarMod::apiFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 7, 'native' =>false));

    $keys = array_merge(array_keys($itemtypes),array_keys($extensionitemtypes));
    $values = array_merge(array_values($itemtypes),array_values($extensionitemtypes));
    return array_combine($keys,$values);
}
?>
