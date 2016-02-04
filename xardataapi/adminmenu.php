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
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function payments_dataapi_adminmenu()
    {
        return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Payments Overview')),
            array('mask' => 'ManagePayments', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the Payments master tables'), 'label' => xarML('Master Tables')),
            array('mask' => 'AdminPayments', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Payments configuration'), 'label' => xarML('Modify Configuration')),
        );
    }
?>