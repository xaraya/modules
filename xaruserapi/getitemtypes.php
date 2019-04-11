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
 * Return the itemtypes of the scraper module
 *
 */
function scraper_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[1] = array('label' => xarML('Native Scraper'),
                          'title' => xarML('View Scraper'),
                          'url'   => xarModURL('scraper','user','view')
                         );

    $extensionitemtypes = xarMod::apiFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 30228, 'native' => false));

    /* TODO: activate this code when we move to php5
    $keys = array_merge(array_keys($itemtypes),array_keys($extensionitemtypes));
    $values = array_merge(array_values($itemtypes),array_values($extensionitemtypes));
    return array_combine($keys,$values);
    */

    $types = array();
    foreach ($itemtypes as $key => $value) $types[$key] = $value;
    foreach ($extensionitemtypes as $key => $value) $types[$key] = $value;
    return $types;
}
?>