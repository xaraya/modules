<?php
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
        if(!xarVarFetch('tplmodule',    'isset', $tplmodule,   'dynamicdata', XARVAR_NOT_REQUIRED)) {return;}

        if (!xarSecConfirmAuthKey()) return;

        $myobject = & DataObjectMaster::getObject(array('objectid' => $objectid,
                                             'itemid'   => $itemid));
        $isvalid = $myobject->checkInput();

        // recover any session var information
        $data = xarModAPIFunc('dynamicdata','user','getcontext',array('module' => $tplmodule));
        extract($data);

        if (!empty($preview) || !$isvalid) {
            $data = array_merge($data, xarModAPIFunc('dynamicdata','admin','menu'));

            $data['object'] = & $myobject;

            $data['authid'] = xarSecGenAuthKey();
            $data['preview'] = $preview;
            if (!empty($return_url)) {
                $data['return_url'] = $return_url;
            }

            // Makes this hooks call explictly from DD
            //$modinfo = xarModGetInfo($myobject->moduleid);
            $modinfo = xarModGetInfo(182);
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

        if (!empty($return_url)) {
            xarResponseRedirect($return_url);
        } else {
            xarResponseRedirect(xarModURL('dynamicdata', 'admin', 'view',
                                          array(
                                          'itemid' => $myobject->objectid,
                                          'tplmodule' => $tplmodule
                                          )));
        }
        return true;
    }

?>