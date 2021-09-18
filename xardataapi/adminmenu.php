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
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
    function sitemapper_dataapi_adminmenu()
    {
        return [
            ['includes' => ['main','overview'], 'target' => 'main', 'label' => xarML('Sitemapper Overview')],
//            array('mask' => 'EditSitemapper', 'includes' => 'view', 'target' => 'sitetests', 'title' => xarML('Display the site test suites'), 'label' => xarML('Site Tests')),
            ['mask' => 'EditSitemapper', 'includes' => 'view', 'target' => 'testpage', 'title' => xarML('Run the unit test suites'), 'label' => xarML('Run Xaraya Unit Tests')],
            ['mask' => 'EditSitemapper', 'target' => 'othertests', 'title' => xarML('Run installation scans'), 'label' => xarML('Run Other Xaraya Tests')],
//            array('mask' => 'ManageSitemapper', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')),
            ['mask' => 'AdminSitemapper', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Sitemapper configuration'), 'label' => xarML('Modify Config')],
        ];
    }
