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
 *
 * Table information
 *
 */

function scraper_xartables()
{
    // Initialise table array
    $xartable = array();

    $xartable['scraper_tags']          = xarDB::getPrefix() . '_scraper_tags';

    // Return the table information
    return $xartable;
}
?>