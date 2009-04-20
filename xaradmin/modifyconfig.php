<?php

// Use this version of the modifyconfig file when creating utility modules

function query_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminQuery')) return;
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'query_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'query', XARVAR_NOT_REQUIRED)) return;
    $hooks = xarModCallHooks('module', 'getconfig', 'query');
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
    switch (strtolower($phase)) {
        case 'modify':
        default:
            switch ($data['tab']) {
                case 'query_general':
                    break;
                default:
                    break;
            }

            break;

        case 'update':
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('query', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('query', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('query', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('debugmode', 'checkbox', $debugmode, xarModVars::get('query', 'debugmode'), XARVAR_NOT_REQUIRED)) return;

            $modvars = array(
                            'debugmode',
                            );

            if ($data['tab'] == 'query_general') {
                xarModVars::set('query', 'itemsperpage', $itemsperpage);
                xarModVars::set('query', 'supportshorturls', $shorturls);
                xarModVars::set('query', 'useModuleAlias', $useModuleAlias);
                xarModVars::set('query', 'aliasname', $aliasname);
                foreach ($modvars as $var)  xarModVars::set('query', $var, $$var);
            }
            $regid = xarMod::getRegID($tabmodule);
            foreach ($modvars as $var)  xarModItemVars::set('query', $var, $$var, $regid);

            // Get the users to be shown the debug messages
            if (!xarVarFetch('debugusers', 'str', $candidates, '', XARVAR_NOT_REQUIRED)) return;
            if (empty($candidates)) {
                $candidates = array();
            } else {
                $candidates = explode(',',$candidates);
            }
            $newusers = array();
            foreach ($candidates as $candidate) {
                $user = xarModAPIFunc('roles','user','get',array('uname' => trim($candidate)));
                if(!empty($user)) $newusers[$user['uname']] = array('id' => $user['id']);
            }
            xarModVars::set('query', 'debugusers', serialize($newusers));

            xarResponse::Redirect(xarModURL('query', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));
            // Return
            return true;
            break;

    }
    $data['hooks'] = $hooks;
    $data['tabmodule'] = $tabmodule;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>
