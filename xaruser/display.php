<?php

// Display one reflection item, based on the id
include_once 'modules/autodoc/xarclass/reflection.php';
function autodoc_user_display($args=array())
{
    $data = array();
    xarVarFetch('itemtype','int::',$itemtype,null,XARVAR_DONT_SET);
    xarVarFetch('itemname','str::',$itemname,null,XARVAR_DONT_SET);
    extract($args);
    if(!isset($itemtype)) throw new EmptyParameterException('itemtype');
    if(!isset($itemname)) throw new EmptyParameterException('itemname');
    
    // Gather info
    $rc =& ReflectionInfo::GetInfo($itemname,$itemtype);
    $data['itemtype'] = $itemtype; 
    $data['itemname'] = $itemname;
    $data['itemInfo'] = $rc->toArray();

    return xarTplModule('autodoc','user','display',$data);
}
?>