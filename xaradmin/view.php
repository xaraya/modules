<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * View items of the realms object
 *
 */
function realms_admin_view($args)
{
    if (!xarSecurity::check('ManageRealms')) {
        return;
    }

    $modulename = 'realms';

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
