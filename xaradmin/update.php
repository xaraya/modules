<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    sys::import('modules.dynamicdata.class.objects.master');
    function calendar_admin_update($args)
    {
        extract($args);

        if (!xarVar::fetch('objectid', 'isset', $objectid, null, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('join', 'isset', $join, null, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('table', 'isset', $table, null, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('tplmodule', 'isset', $tplmodule, 'calendar', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('return_url', 'isset', $return_url, null, xarVar::DONT_SET)) {
            return;
        }
        if (!xarVar::fetch('preview', 'isset', $preview, 0, xarVar::NOT_REQUIRED)) {
            return;
        }

        if (!xarSec::confirmAuthKey()) {
            return;
        }
        $myobject = DataObjectMaster::getObject(array('objectid' => $objectid,
                                             'join'     => $join,
                                             'table'    => $table,
                                             'itemid'   => $itemid));
        $itemid = $myobject->getItem();
        // if we're editing a dynamic property, save its property type to cache
        // for correct processing of the configuration rule (ValidationProperty)
        if ($myobject->objectid == 2) {
            xarVar::setCached('dynamicdata', 'currentproptype', $myobject->properties['type']);
        }

        $isvalid = $myobject->checkInput(array(), 0, 'dd');

        // recover any session var information
        $data = xarMod::apiFunc('dynamicdata', 'user', 'getcontext', array('module' => $tplmodule));
        extract($data);

        if (!empty($preview) || !$isvalid) {
            $data = array_merge($data, xarMod::apiFunc('dynamicdata', 'admin', 'menu'));
            $data['object'] = & $myobject;

            $data['objectid'] = $myobject->objectid;
            $data['itemid'] = $itemid;
            $data['authid'] = xarSec::genAuthKey();
            $data['preview'] = $preview;
            if (!empty($return_url)) {
                $data['return_url'] = $return_url;
            }

            // Makes this hooks call explictly from DD
            // $modinfo = xarMod::getInfo($myobject->moduleid);
            $modinfo = xarMod::getInfo(182);
            $item = array();
            foreach (array_keys($myobject->properties) as $name) {
                $item[$name] = $myobject->properties[$name]->value;
            }
            $item['module'] = $modinfo['name'];
            $item['itemtype'] = $myobject->itemtype;
            $item['itemid'] = $myobject->itemid;
            $hooks = array();
            $hooks = xarModHooks::call('item', 'modify', $myobject->itemid, $item, $modinfo['name']);
            $data['hooks'] = $hooks;

            return xarTpl::module($tplmodule, 'user', 'modify', $data);
        }

        // Valid and not previewing, update the object

        $itemid = $myobject->updateItem();
        if (!isset($itemid)) {
            return;
        } // throw back

        // If we are here then the update is valid: reset the session var
        xarSession::setVar('ddcontext.' . $tplmodule, array('tplmodule' => $tplmodule));

        $item = $myobject->getFieldValues();
        $item['module'] = 'calendar';
        $item['itemtype'] = 1;
        xarModHooks::call('item', 'update', $itemid, $item);

        if (!empty($return_url)) {
            xarController::redirect($return_url);
        } elseif ($myobject->objectid == 2) { // for dynamic properties, return to modifyprop
            $objectid = $myobject->properties['objectid']->value;
            xarController::redirect(xarController::URL(
                'dynamicdata',
                'admin',
                'modifyprop',
                array('itemid' => $objectid)
            ));
        } else {
            xarController::redirect(xarController::URL(
                'dynamicdata',
                'admin',
                'view',
                array(
                                          'itemid' => $objectid,
                                          'tplmodule' => $tplmodule
                                          )
            ));
        }
        return true;
    }
