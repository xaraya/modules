<?php

function subitems_admin_ddobjectlink_new($args)
{
    extract($args);

    if(!xarVarFetch('confirm','str:1',$confirm,'')) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminSubitems')) return;

    if($confirm)    {
        $result_array = xarVarBatchFetch(
        array('objectid','int:1','objectid'),
          array('modid','str:1:','module'),
           array('itemtype','int:0:','itemtype'),
        array('template','str:0:','template')
        );
    }
    else    {
        $result_array['no_errors'] = true;
        $result_array['module'] = array('value' => '','error' => '');
        $result_array['itemtype'] = array('value' => '','error' => '');
        $result_array['template'] = array('value' => '','error' => '');
        $result_array['objectid'] = array('value' => '','error' => '');
    }

    // if(!xarVarFetch('objectid','int:1:',$objectid)) return;


    if(($result_array['no_errors'] == true) && !empty($confirm))    {
        if (!xarSecConfirmAuthKey()) return;

        if(!xarModAPIFunc('subitems','admin','ddobjectlink_create',array(
            "objectid" => $result_array['objectid']['value'],
            "module" => $result_array['module']['value'],
            "itemtype" => $result_array['itemtype']['value'],
            "template" => $result_array['template']['value']
            ))) return;

        xarResponseRedirect(xarModURL('subitems','admin','ddobjectlink_view'));
        return true;
    }

    $data = xarModAPIFunc('subitems','admin','menu');
    $data = array_merge($result_array,$data);
    $data['submitbutton'] = xarML("Create New DDSubobjectlink");
    $data['heading'] = xarML("Add Link to Subitems");

    return $data;
}

?>
