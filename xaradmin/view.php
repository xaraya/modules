<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * View items of the cacher object
 *
 */
function cacher_admin_view($args)
{
    if (!xarSecurity::check('ManageCacher')) {
        return;
    }

    $modulename = 'cacher';

    // Define which object will be shown
    if (!xarVar::fetch('objectname', 'str', $objectname, null, xarVar::DONT_SET)) {
        return;
    }
    if (!empty($objectname)) {
        xarModUserVars::set($modulename, 'defaultmastertable', $objectname);
    }

    // Set a return url
    xarSession::setVar('ddcontext.' . $modulename, ['return_url' => xarServer::getCurrentURL()]);

    // Get the available dropdown options
    $object = DataObjectMaster::getObjectList(['objectid' => 1]);
    $data['objectname'] = xarModUserVars::get($modulename, 'defaultmastertable');
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
