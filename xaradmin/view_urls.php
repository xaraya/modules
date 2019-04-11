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
 * View items of the scraper_urls object
 *
 */
function scraper_admin_view_urls($args)
{
    if (!xarSecurityCheck('ManageScraper')) return;

    $modulename = 'scraper';

    // Define which object will be shown
    if (!xarVarFetch('objectname', 'str', $data['objectname'], 'scraper_urls', XARVAR_DONT_SET)) return;

    return $data;
}
?>