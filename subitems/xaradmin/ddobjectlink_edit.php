<?php

function subitems_admin_ddobjectlink_edit($args)
{
    extract($args);

    if(!xarVarFetch('objectid','int:1:',$objectid)) return;
    if(!xarVarFetch('confirm','str:0',$confirm,'',XARVAR_NOT_REQUIRED)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminSubitems')) return;

    if(!$ddobjectlink = xarModAPIFunc('subitems','user','ddobjectlink_get',array('objectid' => $objectid))) return;
    // nothing to see here
    if (empty($ddobjectlink['objectid'])) return xarML('This item does not exist');

    if($confirm)    {
        $result_array = xarVarBatchFetch(
          array('modid','str:1:','module'),
           array('itemtype','int:0:','itemtype'),
        array('template','str:0:','template')
        );
    }
    else    {
        $result_array['no_errors'] = true;
        $result_array['module'] = array('value' => $ddobjectlink['module'],'error' => '');
        $result_array['itemtype'] = array('value' => $ddobjectlink['itemtype'],'error' => '');
        $result_array['template'] = array('value' => $ddobjectlink['template'],'error' => '');
    }

    // if(!xarVarFetch('objectid','int:1:',$objectid)) return;


    if(($result_array['no_errors'] == true) && !empty($confirm))    {
        if (!xarSecConfirmAuthKey()) return;

        if(!xarModAPIFunc('subitems','admin','ddobjectlink_update',array(
            "objectid" => $objectid,
            "module" => $result_array['module']['value'],
            "itemtype" => $result_array['itemtype']['value'],
            "template" => $result_array['template']['value']
            ))) return;

        xarResponseRedirect(xarModURL('subitems','admin','ddobjectlink_view'));
        return true;
    }

    $data = xarModAPIFunc('subitems','admin','menu');
    $data = array_merge($result_array,$data);
    $data['submitbutton'] = xarML("Update DDSubobjectlink");
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                array('objectid' => $objectid));
    if (!empty($objectinfo)) {
        $data['label'] = $objectinfo['label'];
    } else {
        $data['label'] = xarML('Unknown');
    }
    $data['heading'] = xarML('Edit Link to #(1)',$data['label']);
    $data['objectid'] = $objectid;

    return $data;
}

?>
