<?php

/**
 * create new xlink definition
 */
function xlink_admin_new($args)
{ 
    extract($args);

    if (!xarVarFetch('confirm',  'isset', $confirm,  NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminXLink')) return;

    $data = array();
    $data['object'] =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'xlink'));
    if (!isset($data['object'])) return;

    if (!empty($confirm)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return; 

        // check the input values for this object
        $isvalid = $data['object']->checkInput();
        if ($isvalid) {
            // create the item here
            $itemid = $data['object']->createItem();
            if (empty($itemid)) return; // throw back

            // let's go back to the admin view
            xarResponseRedirect(xarModURL('xlink', 'admin', 'view'));
            return true;
        }
    }

    $item = array();
    $item['module'] = 'xlink';
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['confirm'] = xarML('Create');
    return $data;
}

?>
