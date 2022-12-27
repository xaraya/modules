<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * display keywords entry
 *
 * @param $args['itemid'] item id of the keywords entry
 * @checkme: this appears to display a link to a display of an item, why is this needed?
 * @return array Item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_user_display($args)
{
    if (!xarSecurity::check('ReadKeywords')) {
        return;
    }

    xarVar::fetch('itemid', 'id', $itemid, '', xarVar::DONT_SET);
    extract($args);

    if (empty($itemid)) {
        return [];
    }
    $items = xarMod::apiFunc(
        'keywords',
        'user',
        'getitems',
        ['id' => $itemid]
    );
    if (!isset($items)) {
        return;
    }
    if (!isset($items[$itemid])) {
        return [];
    }

    $item = $items[$itemid];
    if (count($item) == 0 || empty($item['moduleid'])) {
        return [];
    }

    $modinfo = xarMod::getInfo($item['moduleid']);
    if (!isset($modinfo) || empty($modinfo['name'])) {
        return [];
    }

    if (!empty($item['itemtype'])) {
        // Get the list of all item types for this module (if any)
        $mytypes = xarMod::apiFunc(
            $modinfo['name'],
            'user',
            'getitemtypes',
            // don't throw an exception if this function doesn't exist
            [],
            0
        );
        if (isset($mytypes) && isset($mytypes[$item['itemtype']])) {
            $item['modname'] = $mytypes[$item['itemtype']]['label'];
        } else {
            $item['modname'] = ucwords($modinfo['name']);
        }
    } else {
        $item['modname'] = ucwords($modinfo['name']);
    }

    $itemlinks = xarMod::apiFunc(
        $modinfo['name'],
        'user',
        'getitemlinks',
        ['itemtype' => $item['itemtype'],
                                     'itemids' => [$item['itemid']], ],
        0
    );

    if (isset($itemlinks[$item['itemid']]) && !empty($itemlinks[$item['itemid']]['url'])) {
        // normally we should have url, title and label here
        foreach ($itemlinks[$item['itemid']] as $field => $value) {
            $item[$field] = $value;
        }
    } else {
        $item['url'] = xarController::URL(
            $modinfo['name'],
            'user',
            'display',
            ['itemtype' => $item['itemtype'],
                                       'itemid' => $item['itemid'], ]
        );
    }
    return $item;
}
