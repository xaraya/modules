<?php


function bkview_user_graphview($args)
{
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('start','str::',$start,'-3d',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('end','str::',$end,'+', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('file','str::',$file,'ChangeSet',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('spc','checkbox::',$spc,1,XARVAR_NOT_REQUIRED)) return;
    
    // Pass the necessary data to the template
    $data = array();
    $data['pageinfo'] = xarML('History graph of #(1)',($file =='ChangeSet') ? 'Repository' : $file);
    $data['repoid']   = $repoid;
    $data['start'] = $start;
    $data['end'] = $end;
    $data['file'] = $file;
    $data['spc'] = empty($spc) ? false : true;
    return $data;
}





?>