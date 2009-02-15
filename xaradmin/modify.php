<?php

function dossier_admin_modify($args)
{
	extract($args);
    
    if (!xarVarFetch('contactid',     'id',     $contactid)) return;
    if (!xarVarFetch('returnurl',     'str::',     $returnurl,     $returnurl,     XARVAR_NOT_REQUIRED)) return;
	
    if (!empty($objectid)) {
        $contactid = $objectid;
    }
	$item = xarModAPIFunc('dossier',
                         'user',
                         'get',
                         array('contactid' => $contactid));
	
	if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('TeamDossierAccess', 1, 'Contact', $item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid'])) {
        return;
    }
    
    $data = xarModAPIFunc('dossier','admin','menu');
    
	$data['contactid'] = $item['contactid'];
	
    $data['authid'] = xarSecGenAuthKey();

	$item['module'] = 'dossier';

	$data['item'] = $item;

	$data['returnurl'] = $returnurl;

    $hooks = xarModCallHooks('item','modify',$contactid,$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    
    return $data;
}

?>
