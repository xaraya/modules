<?php
function ephemerids_admin_modify($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('eid','int:1:',$eid)) return;
    if (!xarVarFetch('objectid','str:1:',$objectid,$eid,XARVAR_NOT_REQUIRED)) return;
    extract($args);
    // The user API function is called. 
    $data = xarModAPIFunc('ephemerids',
                         'user',
                         'get',
                         array('eid' => $eid));

    if ($data == false) return;
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;
    // Get menu variables 
    $hooks = xarModCallHooks('item','modify',$eid,$data);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    // Return the template variables defined in this function
    return $data;
}
?>