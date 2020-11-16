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
 * Display an item of the scraper object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function scraper_user_display()
{
    if (!xarSecurity::check('ReadScraper')) {
        return;
    }
    xarTpl::setPageTitle('Display Scraper');

    if (!xarVar::fetch('name', 'str', $name, 'scraper_scraper', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'scraper';

    return $data;
}
