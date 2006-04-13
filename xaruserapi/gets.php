<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
  Gets items of a DynamicData object
  @author Brian McGilligan
  @param $args['itemtype'] - Item type
  @returns list of items of the item type
*/
function helpdesk_userapi_gets($args)
{
    extract($args);

    $modid = xarModGetIDFromName('helpdesk');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = $itemtype;

    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', $info);

    return $items;
}
?>
