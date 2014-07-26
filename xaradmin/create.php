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
    function calendar_admin_create($args)
    {
        extract($args);

        if (!xarVarFetch('objectid',    'isset', $objectid,   NULL, XARVAR_DONT_SET)) return;
        if (!xarVarFetch('itemid',      'isset', $itemid,     0,    XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('preview',     'isset', $preview,    0,    XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('return_url',  'isset', $return_url, NULL, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('join',        'isset', $join,       NULL, XARVAR_DONT_SET)) {return;}
        if (!xarVarFetch('table',       'isset', $table,      NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('template',     'isset', $template,   NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('tplmodule',    'isset', $tplmodule,   'calendar', XARVAR_NOT_REQUIRED)) {return;}

        if (!xarSecConfirmAuthKey()) return;

        $myobject = DataObjectMaster::getObject(array('objectid' => $objectid,
                                             'itemid'   => $itemid));
        $isvalid = $myobject->checkInput();

        // recover any session var information
        $data = xarMod::apiFunc('dynamicdata','user','getcontext',array('module' => $tplmodule));
        extract($data);

        if (!empty($preview) || !$isvalid) {
            $data = array_merge($data, xarMod::apiFunc('dynamicdata','admin','menu'));

            $data['object'] = & $myobject;

            $data['authid'] = xarSecGenAuthKey();
            $data['preview'] = $preview;
            if (!empty($return_url)) {
                $data['return_url'] = $return_url;
            }

            // Makes this hooks call explictly from DD
            //$modinfo = xarMod::getInfo($myobject->moduleid);
            $modinfo = xarMod::getInfo(182);
            $item = array();
            foreach (array_keys($myobject->properties) as $name) {
                $item[$name] = $myobject->properties[$name]->value;
            }
            $item['module'] = $modinfo['name'];
            $item['itemtype'] = $myobject->itemtype;
            $item['itemid'] = $myobject->itemid;
            $hooks = array();
            $hooks = xarModCallHooks('item', 'new', $myobject->itemid, $item, $modinfo['name']);
            $data['hooks'] = $hooks;

            if(!isset($template)) {
                $template = $myobject->name;
            }
            return xarTplModule($tplmodule,'user','new',$data,$template);
        }

        $itemid = $myobject->createItem();

       // If we are here then the create is valid: reset the session var
        xarSession::setVar('ddcontext.' . $tplmodule, array('tplmodule' => $tplmodule));

        if (empty($itemid)) return; // throw back

        $item = $myobject->getFieldValues();
        $item['module'] = 'calendar';
        $item['itemtype'] = 1;
        xarModCallHooks('item', 'create', $itemid, $item);

        if (!empty($return_url)) {
            xarController::redirect($return_url);
        } else {
            xarController::redirect(xarModURL('dynamicdata', 'admin', 'view',
                                          array(
                                          'itemid' => $myobject->objectid,
                                          'tplmodule' => $tplmodule
                                          )));
        }
        return true;
    }

?>