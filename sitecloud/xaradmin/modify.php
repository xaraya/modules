<?php
/**
 * modify an item
 * @param 'id' the id of the headline to be modified
 */
function sitecloud_admin_modify($args)
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('id','int:1:',$id)) return;
    if (!xarVarFetch('obid','str:1:',$obid,$id,XARVAR_NOT_REQUIRED)) return;

    // Security Check
	if(!xarSecurityCheck('Editsitecloud')) return;
    $data = array();
    $data = xarModAPIFunc('sitecloud',
                          'user',
                          'get',
                          array('id' => $id));

    if ($data == false) return;
    $data['module']         = 'sitecloud';
    $data['itemtype']       = NULL; // forum
    $data['itemid']         = $id;
    $hooks = xarModCallHooks('item','modify',$id,$data);
    if (empty($hooks)) {
        $data['hooks']      = '';
    } elseif (is_array($hooks)) {
        $data['hooks']      = join('',$hooks);
    } else {
        $data['hooks']      = $hooks;
    }
    $data['submitlabel']    = xarML('Submit');
    $data['authid']         = xarSecGenAuthKey();
    return $data;
}
?>
