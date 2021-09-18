<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Return the itemtypes of the reminders module
 *
 */
function reminders_userapi_getitemtypes($args)
{
    $itemtypes = [];

    $itemtypes[1] = ['label' => xarML('Native Reminders'),
                          'title' => xarML('View Reminders'),
                          'url'   => xarController::URL('reminders', 'user', 'view'),
                         ];

    $extensionitemtypes = xarMod::apiFunc('dynamicdata', 'user', 'getmoduleitemtypes', ['moduleid' => 30227, 'native' => false]);

    /* TODO: activate this code when we move to php5
    $keys = array_merge(array_keys($itemtypes),array_keys($extensionitemtypes));
    $values = array_merge(array_values($itemtypes),array_values($extensionitemtypes));
    return array_combine($keys,$values);
    */

    $types = [];
    foreach ($itemtypes as $key => $value) {
        $types[$key] = $value;
    }
    foreach ($extensionitemtypes as $key => $value) {
        $types[$key] = $value;
    }
    return $types;
}
