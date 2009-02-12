<?php

function dossier_user_relationships($args)
{
    
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    
    $data = array();
    
    $data['contactid'] = $contactid;
    
    $relationships = xarModAPIFunc('dossier','relationships','getall',array('contactid' => $contactid));
    
    if($relationships === false) return;
    
    $data['relationships'] = $relationships;
    
    return $data;
}

?>