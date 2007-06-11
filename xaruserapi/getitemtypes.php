<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @return array Array containing the item types and their description
 */
function articles_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[300] = array('label' => xarML('Bare Article'),
                          'title' => xarML('View Bare Article'),
                          'url'   => xarModURL('articles','user','view')
                         );
    // Get publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    foreach ($pubtypes as $id => $pubtype) {
        $itemtypes[$id] = array('label' => xarVarPrepForDisplay($pubtype['descr']),
                                'title' => xarVarPrepForDisplay(xarML('Display #(1)',$pubtype['descr'])),
                                'url'   => xarModURL('articles','user','view',array('ptid' => $id))
                               );
    }

    $extensionitemtypes = xarModAPIFunc('dynamicdata','user','getmoduleitemtypes',array('moduleid' => 151, 'native' =>false));

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
