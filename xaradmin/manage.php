<?php
function fulltext_admin_manage()
{
    //if(!xarSecurityCheck('AdminFulltext')) return;
    
    if (!xarVarFetch('searchmodule', 'pre:trim:lower:str:1:', $searchmodule, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('searchitemtype', 'int', $searchitemtype, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'pre:trim:lower:enum:update', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    
    if ($phase == 'update') {
        //if (!xarSecConfirmAuthKey())
        //    return xarTplModule'privileges', 'user', 'error', array('layout' => 'bad_author')); 
        if (!xarMod::apiFunc('fulltext', 'hooks', 'moduleupdateconfig',
            array('extrainfo' => array('module' => $searchmodule, 'itemtype' => $searchitemtype)))) return;
        $return_url = xarModURL('fulltext', 'admin', 'manage', 
            array('searchmodule' => $searchmodule, 'searchitemtype' => $searchitemtype));
        return xarController::redirect($return_url);
    }
    
    // $subjects = xarHooks::getObserverSubjects('fulltext');
    $subjects = xarMod::apiFunc('modules', 'admin', 'gethookedmodules',
        array(
            'hookModName' => 'fulltext',
        ));
    if (!empty($subjects)) {
        foreach ($subjects as $modname => $hooks) {
            $modinfo = xarMod::getInfo(xarMod::getRegID($modname));
            try {
                $itemtypes = xarMod::apiFunc($modname, 'user', 'getitemtypes');
            } catch (Exception $e) {
                $itemtypes = array();
            }
            $modinfo['itemtypes'] = array();
            foreach ($itemtypes as $typeid => $typeinfo) {
                if (!isset($hooks[0]) && !isset($hooks[$typeid])) continue; // not hooked
                $modinfo['itemtypes'][$typeid] = $typeinfo;
            }
            $subjects[$modname] += $modinfo;
        }
    }
        
    $data = array();
    $data['subjects'] = $subjects;
    $data['searchmodule'] = $searchmodule;
    $data['searchitemtype'] = $searchitemtype;
    $data['modulemodifyconfig'] = xarMod::guiFunc('fulltext', 'hooks', 'modulemodifyconfig',
        array('extrainfo' => array('module' => $searchmodule, 'itemtype' => $searchitemtype)));

    return $data;
}
?>