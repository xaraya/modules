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
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
function cacher_dataapi_adminmenu()
{
    return array(
        array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Cacher Overview')),
        array('mask' => 'ManageCacher', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')),
        array('mask' => 'AdminCacher', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Cacher configuration'), 'label' => xarML('Modify Config')),
    );
}
