<?php

function subitems_admin_ddobjectlink_delete($args)
{
 	extract($args);

   if(!xarVarFetch('objectid','int:1:',$objectid)) return;
   if(!xarVarFetch('confirm','str:1:',$confirm,'',XARVAR_NOT_REQUIRED)) return;

   if($confirm)	{
        if (!xarSecConfirmAuthKey()) return;

   		if(!xarModAPIFunc('subitems','admin','ddobjectlink_delete',array('objectid' => $objectid))) return;

         xarResponseRedirect(xarModURL('subitems','admin','ddobjectlink_view'));

   		return true;

   }
   $data = xarModAPIFunc('subitems','admin','menu');
   $item = xarModAPIFunc('subitems','user','ddobjectlink_get',array('objectid' => $objectid));
    // nothing to see here
   if (empty($item) || empty($item['objectid'])) return xarML('This item does not exist');

    $data = array_merge($item,$data);
    $objectinfo = xarModAPIFunc('dynamicdata','user','getobjectinfo',
                                array('objectid' => $objectid));
    if (!empty($objectinfo)) {
        $data['label'] = $objectinfo['label'];
    } else {
        $data['label'] = xarML('Unknown');
    }
    return $data;
}

?>
