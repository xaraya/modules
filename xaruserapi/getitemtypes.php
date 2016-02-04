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
 * Return the itemtypes of the payments module
 *
 */
    function payments_userapi_getitemtypes($args)
    {
        $itemtypes = array();

        $itemtypes[1] = array('label' => xarML('Native Payments'),
                              'title' => xarML('View Payments'),
                              'url'   => xarModURL('payments','user','view')
                             );

        $extensionitemtypes = xarModAPIFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 30052, 'native' => false));

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
