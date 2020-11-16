<?php
/**
 * Scraper Module
 *
 * @package modules
 * @subpackage scraper
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * View items of the objects of the scraper module
 *
 */
function scraper_admin_view_tables($args)
{
    if (!xarSecurity::check('ManageScraper')) {
        return;
    }

    $modulename = 'scraper';

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
