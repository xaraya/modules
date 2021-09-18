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
 * Return the options for the admin menu
 *
 */

// TODO: turn this into an xml file
function scraper_dataapi_adminmenu()
{
    return [
        ['includes' => ['main','overview'], 'target' => 'main', 'label' => xarML('Scraper Overview')],
        ['mask' => 'ManageScraper', 'includes' => 'view', 'target' => 'view', 'title' => xarML('Manage the master tables of thsi module'), 'label' => xarML('Master Tables')],
        ['mask' => 'AdminScraper', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Scraper configuration'), 'label' => xarML('Modify Config')],
    ];
}
