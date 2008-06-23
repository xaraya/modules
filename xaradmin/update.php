<?php

    sys::import('modules.dynamicdata.class.objects.master');
    function calendar_admin_update($args)
    {
        extract($args);

        if(!xarVarFetch('objectid',   'isset', $objectid,    NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('itemid',     'isset', $itemid,      NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('join',       'isset', $join,        NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('table',      'isset', $table,       NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('tplmodule',  'isset', $tplmodule,   'calendar', XARVAR_NOT_REQUIRED)) {return;}
        if(!xarVarFetch('return_url', 'isset', $return_url,  NULL, XARVAR_DONT_SET)) {return;}
        if(!xarVarFetch('preview',    'isset', $preview,     0, XARVAR_NOT_REQUIRED)) {return;}

        if (!xarSecConfirmAuthKey()) return;
        $myobject = & DataObjectMaster::getObject(array('objectid' => $objectid,
                                             'join'     => $join,
                                             'table'    => $table,
                                             'itemid'   => $itemid));
        $itemid = $myobject->getItem();
        // if we're editing a dynamic property, save its property type to cache
        // for correct processing of the configuration rule (ValidationProperty)
        if ($myobject->objectid == 2) {
            xarVarSetCached('dynamicdata','currentproptype', $myobject->properties['type']);
        }

        $isvalid = $myobject->checkInput(array(), 0, 'dd');

        // recover any session var information
        $data = xarModAPIFunc('dynamicdata','user','getcontext',array('module' => $tplmodule));
        extract($data);

        if (!empty($preview) || !$isvalid) {
            $data = array_merge($data, xarModAPIFunc('dynamicdata','admin','menu'));
            $data['object'] = & $myobject;

            $data['objectid'] = $myobject->objectid;
            $data['itemid'] = $itemid;
            $data['authid'] = xarSecGenAuthKey();
            $data['preview'] = $preview;
            if (!empty($return_url)) {
                $data['return_url'] = $return_url;
            }

            // Makes this hooks call explictly from DD
            // $modinfo = xarModGetInfo($myobject->moduleid);
            $modinfo = xarModGetInfo(182);
            $item = array();
            foreach (array_keys($myobject->properties) as $name) {
                $item[$name] = $myobject->properties[$name]->value;
            }
            $item['module'] = $modinfo['name'];
            $item['itemtype'] = $myobject->itemtype;
            $item['itemid'] = $myobject->itemid;
            $hooks = array();
            $hooks = xarModCallHooks('item', 'modify', $myobject->itemid, $item, $modinfo['name']);
            $data['hooks'] = $hooks;

            return xarTplModule($tplmodule,'user','modify', $data);
        }

        // Valid and not previewing, update the object

        $itemid = $myobject->updateItem();
        if (!isset($itemid)) return; // throw back

         // If we are here then the update is valid: reset the session var
        xarSession::setVar('ddcontext.' . $tplmodule, array('tplmodule' => $tplmodule));

        if (!empty($return_url)) {
            xarResponseRedirect($return_url);
        } elseif ($myobject->objectid == 2) { // for dynamic properties, return to modifyprop
            $objectid = $myobject->properties['objectid']->value;
            xarResponseRedirect(xarModURL('dynamicdata', 'admin', 'modifyprop',
                                          array('itemid' => $objectid)));
        } else {
            xarResponseRedirect(xarModURL('dynamicdata', 'admin', 'view',
                                          array(
                                          'itemid' => $objectid,
                                          'tplmodule' => $tplmodule
                                          )));
        }
        return true;
    }
?>
