<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * View items of the sitemapper object
 *
 */
    function sitemapper_admin_view($args)
    {
        if (!xarSecurityCheck('ManageSitemapper')) {
            return;
        }

        $modulename = 'sitemapper';

        // Define which object will be shown
        if (!xarVarFetch('objectname', 'str', $objectname, null, XARVAR_DONT_SET)) {
            return;
        }
        if (!empty($objectname)) {
            xarModVars::set($modulename, 'defaultmastertable', $objectname);
        }

        // Set a return url
        xarSession::setVar('ddcontext.' . $modulename, array('return_url' => xarServer::getCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModVars::get($modulename, 'defaultmastertable');
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
