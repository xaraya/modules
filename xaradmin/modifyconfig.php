<?php
function fulltext_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminFulltext')) return;

    if (!xarVarFetch('phase', 'pre:trim:lower:enum:update',
        $phase, 'form', XARVAR_NOT_REQUIRED)) return;

    $data = array();
    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'fulltext'));
    //$data['module_settings']->setFieldList('items_per_page, use_module_alias, use_module_icons, enable_short_urls');
    $data['module_settings']->setFieldList('items_per_page');
    $data['module_settings']->getItem();
    
    if ($phase == 'update') {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) 
            return xarTpl::module('privileges','user','errors',array('layout' => 'bad_author'));

        $isvalid = $data['module_settings']->checkInput();
        if ($isvalid) {
            $itemid = $data['module_settings']->updateItem();
            xarController::redirect(xarModURL('fulltext', 'admin', 'modifyconfig'));
        }    
    }
    return $data;
}
?>