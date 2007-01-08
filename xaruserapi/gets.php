<?php
/**
 * Gets items of a DynamicData object
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel module development team
 */
/**
 * Gets items of a DynamicData object
 * @author Brian McGilligan
 * @param $args['itemtype'] - Item type
 * @return array list of items of the item type
*/
function sigmapersonnel_userapi_gets($args)
{
    extract($args);

    $modid = xarModGetIDFromName('sigmapersonnel');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = $itemtype;

    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', $info);

    return $items;
}
?>
