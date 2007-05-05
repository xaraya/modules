<?php
    function members_admin_modifyconfig()
    {
        // Security Check
        if (!xarSecurityCheck('AdminMembers')) return;
        if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'members_general', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'members', XARVAR_NOT_REQUIRED)) return;

        sys::import('xaraya.structures.hooks.observer');
        $subject = new HookSubject('members');
        $messenger = $subject->getMessenger();
        $messenger->setHook('module', 'getconfig');

        $hooks = $subject->notify();
        if (!empty($hooks) && isset($hooks['tabs'])) {
            foreach ($hooks['tabs'] as $key => $row) {
                $configarea[$key]  = $row['configarea'];
                $configtitle[$key] = $row['configtitle'];
                $configcontent[$key] = $row['configcontent'];
            }
            array_multisort($configtitle, SORT_ASC, $hooks['tabs']);
        } else {
            $hooks['tabs'] = array();
        }

        $regid = xarModGetIDFromName($tabmodule);
        switch (strtolower($phase)) {
            case 'modify':
            default:
                switch ($data['tab']) {
                    case 'general':
                    default:
                        $objectid = xarModGetUserVar('members', 'object', $regid);
                        $myobject = xarModApiFunc('dynamicdata','user','getobject', array('objectid' => $objectid));
                        $properties = $myobject->getProperties();
                        $activefields = array();
                        foreach ($properties as $property) {
                            if ($property->getDisplayStatus() != DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE) continue;
                            if ($property->source == 'dynamic_data') continue;
                            $activefields[] = array('id'=>$property->name,'name'=>$property->label);
                        }
                        $data['keyfields'] = $activefields;
                        break;
                }

                break;

            case 'update':
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;
                if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('members', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
                if (!xarVarFetch('shorturls', 'checkbox', $shorturls, xarModVars::get('members', 'supportshorturls'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('members', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('members', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultviewtype', 'str', $defaultviewtype, xarModVars::get('members', 'defaultviewtype'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultgroup', 'str', $defaultgroup, xarModVars::get('members', 'defaultgroup'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('alphabet', 'str', $alphabet, implode(',',unserialize(xarModVars::get('members', 'alphabet'))), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('object', 'int', $object, xarModVars::get('members', 'object'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('defaultselectkey', 'str', $defaultselectkey, xarModVars::get('members', 'defaultselectkey'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('usernamevars', 'str', $usernamevars, 'lastname.firstname.id', XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showalltab', 'checkbox', $showalltab, xarModVars::get('members', 'showalltab'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showothertab', 'checkbox', $showothertab, xarModVars::get('members', 'showothertab'), XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('showactiveall', 'checkbox', $showactiveall, xarModVars::get('members', 'showactiveall'), XARVAR_NOT_REQUIRED)) return;

            $modvars = array(
                            'defaultviewtype',
                            'defaultgroup',
                            'object',
                            'defaultselectkey',
                            'usernamevars',
                            'showalltab',
                            'showothertab',
                            'showactiveall',
                            );

                if ($data['tab'] == 'members_general') {
                    xarModVars::set('members', 'itemsperpage', $itemsperpage);
                    xarModVars::set('members', 'supportshorturls', $shorturls);
                    xarModVars::set('members', 'useModuleAlias', $useModuleAlias);
                    xarModVars::set('members', 'aliasname', $aliasname);
                    $alphabet = explode(',',trim($alphabet,','));
                    xarModVars::set('members', 'alphabet', serialize($alphabet));
                    foreach ($modvars as $var)  xarModVars::set('members', $var, $$var);
                }
                foreach ($modvars as $var)  xarModItemVars::set('members', $var, $$var, $regid);

                sys::import('modules.dynamicdata.class.properties.master');
                $picker = DataPropertyMaster::getProperty(array('name' => 'categorypicker'));
                $picker->checkInput('basecid');

                xarResponseRedirect(xarModURL('members', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
                return true;
                break;
        }
        $data['hooks'] = $hooks;
        $data['tabmodule'] = $tabmodule;
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }
?>
