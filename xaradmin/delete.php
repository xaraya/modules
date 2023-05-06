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
 * delete existing keywords assignment
 */
function keywords_admin_delete($args)
{
    if (!xarSecurity::check('ManageKeywords')) return;

    $data = array();

    if (!xarVar::fetch('module_id', 'id',
        $module_id, null, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('itemtype', 'id',
        $itemtype, null, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('itemid', 'id',
        $itemid, null, xarVar::DONT_SET)) return;
    if (!xarVar::fetch('return_url', 'pre:trim:str:1:',
        $return_url, '', xarVar::NOT_REQUIRED)) return;

    if (empty($return_url))
        $return_url = xarController::URL('keywords', 'admin', 'stats',
            array(
                'module_id' => $module_id,
                'itemtype' => $itemtype,
            ));

    if (empty($module_id))
        $invalid[] = 'module_id';
    if (empty($itemid))
        $invalid[] = 'itemid';

    if (!empty($invalid)) {
        $msg = 'Missing #(1) for #(2) module #(3) function #(4)()';
        $vars = array(implode(', ', $invalid), 'keywords', 'admin', 'delete');
        throw new EmptyParameterException($vars, $msg);
    }

    if (!xarVar::fetch('phase', 'pre:trim:lower:enum:confirm',
        $phase, 'form', xarVar::NOT_REQUIRED)) return;

    $modname = xarMod::getName($module_id);

    if ($phase == 'confirm') {
        if (!xarVar::fetch('cancel', 'checkbox',
            $cancel, false, xarVar::NOT_REQUIRED)) return;
        if ($cancel)
            xarController::redirect($return_url);
        if (!xarSec::confirmAuthKey())
            return xarTpl::module('privileges', 'user', 'errors', array('layout' => 'bad_author'));
        // get the index_id for this module/itemtype/item
        $index_id = xarMod::apiFunc('keywords', 'index', 'getid',
            array(
                'module' => $modname,
                'itemtype' => $itemtype,
                'itemid' => $itemid,
            ));

        // delete all keywords associated with this item
        if (!xarMod::apiFunc('keywords', 'words', 'deleteitems',
            array(
                'index_id' => $index_id,
            ))) return;
        xarController::redirect($return_url);
    }

    try {
        $item = xarMod::apiFunc($modname, 'user', 'getitemlinks',
            array(
                'itemtype' => $itemtype,
                'itemids' => array($itemid),
            ));
        $item = reset($item);
    } catch (Exception $e) {
        $item = array(
            'label' => xarML('Item #(1)', $itemid),
            'title' => xarML('Display Item #(1)', $itemid),
            'url' => xarController::URL($modname, 'user', 'display',
                array('itemtype' => $itemtype, 'itemid' => $itemid)),
        );
    }

    $modlist = xarMod::apiFunc('keywords', 'words', 'getmodulecounts',
        array(
            'skip_restricted' => true,
        ));
    $modtypes = array();
    $modules = array();
    foreach ($modlist as $module => $itemtypes) {
        $modules[$module] = xarMod::getBaseInfo($module);
        $modules[$module]['itemtypes'] = $itemtypes;
        if (!isset($modtypes[$module])) {
            try {
                $modtypes[$module] = xarMod::apiFunc($module, 'user', 'getitemtypes');
            } catch (Exception $e) {
                $modtypes[$module] = array();
            }
        }
        foreach ($itemtypes as $typeid => $typeinfo) {
            if (empty($typeid)) continue;
            if (!isset($modtypes[$module][$typeid])) {
                $modtypes[$module][$typeid] = array(
                    'label' => xarML('Itemtype #(1)', $typeid),
                    'title' => xarML('View itemtype #(1) items', $typeid),
                    'url' => xarController::URL($module, 'user', 'view', array('itemtype' => $typeid)),
                );
            }
            $modules[$module]['itemtypes'][$typeid] += $modtypes[$module][$typeid];
        }
    }

    $data['modules'] = $modules;
    $data['module_id'] = $module_id;
    $data['modname'] = $modname;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;
    $data['item'] = $item;
    $data['return_url'] = $return_url;

    $data['display_hook'] = xarMod::guiFunc('keywords', 'user', 'displayhook',
        array(
            'objectid' => $itemid,
            'extrainfo' => array('module' => $modname, 'itemtype' => $itemtype, 'itemid' => $itemid, 'showlabel' => false, 'tpltype' => 'admin'),
        ));

    return $data;
}
?>