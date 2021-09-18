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

    function calendar_admin_view($args)
    {
        if (!xarSecurity::check('ManageCalendar')) {
            return;
        }

        $modulename = 'calendar';

        // Define which object will be shown
        if (!xarVar::fetch('objectname', 'str', $objectname, 'calendar_calendar', xarVar::DONT_SET)) {
            return;
        }
        if (!empty($objectname)) {
            xarModVars::set($modulename, 'defaultmastertable', $objectname);
        }

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, ['return_url' => xarServer::getCurrentURL()]);

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(['objectid' => 1]);
        $data['objectname'] = xarModVars::get($modulename, 'defaultmastertable');
        $items = $object->getItems();
        $options = [];
        foreach ($items as $item) {
            if (strpos($item['name'], $modulename) !== false) {
                $options[] = ['id' => $item['name'], 'name' => $item['name']];
            }
        }
        $data['options'] = $options;
        return $data;
    }
