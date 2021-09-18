<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

function translations_admin_choose_a_object()
{
    // Security Check
    if (!xarSecurity::check('AdminTranslations')) {
        return;
    }

    if (!($objectlist = xarMod::apiFunc('dynamicdata', 'user', 'getobjectlist', ['name' => 'objects']))) {
        return;
    }
    $items = $objectlist->getItems();

    // Sort by object name
    if (!empty($items)) {
        foreach ($items as $key => $row) {
            $name[$key]  = $row['name'];
        }
        array_multisort($name, SORT_ASC, $items);
    }

    $tplData = translations_create_druidbar(CHOOSE, xarMLS::DNTYPE_OBJECT, '', 0);
    $tplData['objectlist'] = $items;
    $tplData['dnType'] = xarMLS::DNTYPE_OBJECT;
    return $tplData;
}
