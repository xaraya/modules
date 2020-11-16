<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * View items of the wurfl object
 *
 */
    function wurfl_admin_view($args)
    {
        if (!xarSecurity::check('ManageWurfl')) {
            return;
        }

        $modulename = 'wurfl';

        // Define which object will be shown
        if (!xarVar::fetch('objectname', 'str', $objectname, null, xarVar::DONT_SET)) {
            return;
        }
        if (!empty($objectname)) {
            xarModUserVars::set($modulename, 'defaultmastertable', $objectname);
        }

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServer::getCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModUserVars::get($modulename, 'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item) {
            if (strpos($item['name'], $modulename) !== false) {
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
            }
        }
        $data['options'] = $options;
        return $data;
    }
