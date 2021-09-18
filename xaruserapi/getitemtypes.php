<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Return the itemtypes of the realms module
 *
 */
    function realms_userapi_getitemtypes($args)
    {
        $itemtypes = [];

        $itemtypes[1] = ['label' => xarML('Native Realms'),
                              'title' => xarML('View Realms'),
                              'url'   => xarController::URL('realms', 'user', 'view'),
                             ];

        $extensionitemtypes = xarMod::apiFunc('dynamicdata', 'user', 'getmoduleitemtypes', ['moduleid' => 30081, 'native' => false]);

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
