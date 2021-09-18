<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_getitemlinks($args)
{
    $itemlinks = [];
    if (!xarSecurity::check('ViewMessages', 0)) {
        return $itemlinks;
    }

    foreach ($args['itemids'] as $itemid) {
        $item = xarMod::apiFunc(
            'roles',
            'user',
            'get',
            ['id' => $itemid]
        );
        if (!isset($item)) {
            return;
        }
        $itemlinks[$itemid] = ['url' => xarController::URL(
            'roles',
            'user',
            'display',
            ['id' => $itemid]
        ),
            'title' => xarML('Display User'),
            'label' => xarVar::prepForDisplay($item['name']), ];
    }
    return $itemlinks;
}
