<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * View items of the payments object
 *
 */
    function payments_admin_view($args)
    {
        // Data Managers have access
        if (!xarSecurityCheck('ManagePayments')) {
            return;
        }

        // Define which object will be shown
        if (!xarVarFetch('objectname', 'str', $objectname, null, XARVAR_DONT_SET)) {
            return;
        }
        if (!empty($objectname)) {
            xarModVars::set('payments', 'defaultmastertable', $objectname);
        }

        // Set a return url
        xarSession::setVar('ddcontext.' . 'payments', array('return_url' => xarServer::getCurrentURL()));

        // Get the available dropdown options
        $object = DataObjectMaster::getObjectList(array('objectid' => 1));
        $data['objectname'] = xarModVars::get('payments', 'defaultmastertable');
        $items = $object->getItems();
        $options = array();
        foreach ($items as $item) {
            if (strpos($item['name'], 'payments') !== false) {
                $options[] = array('id' => $item['name'], 'name' => $item['name']);
            }
        }
        $data['options'] = $options;
        return $data;
    }
