<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @return array Array containing the item types and their description
 */
function publications_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[300] = array('label' => xarML('Bare Publication'),
                          'title' => xarML('View Bare Publication'),
                          'url'   => xarModURL('publications','user','view')
                         );
    // Get publication types
    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

    foreach ($pubtypes as $id => $pubtype) {
        $itemtypes[$id] = array('label' => xarVarPrepForDisplay($pubtype['description']),
                                'title' => xarVarPrepForDisplay(xarML('Display #(1)',$pubtype['description'])),
                                'url'   => xarModURL('publications','user','view',array('ptid' => $id))
                               );
    }

    $extensionitemtypes = xarModAPIFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 30065, 'native' =>false));

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
