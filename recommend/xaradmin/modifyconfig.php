<?php

/**
 * Modify the confirmation email for users
 */
function recommend_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('EditRole')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default:

            $data['title']          = xarModGetVar('recommend', 'title');
            $data['template']       = xarModGetVar('recommend', 'template');
            $data['numbersent']     = xarModGetVar('recommend', 'numbersent');
            $data['lastemailaddy']  = xarModGetVar('recommend', 'lastsentemail');
            $data['lastemailname']  = xarModGetVar('recommend', 'lastsentname');
            $data['date']           = xarModGetVar('recommend', 'date');
            $data['username']       = xarModGetVar('recommend', 'username');
            $data['authid']         = xarSecGenAuthKey(); 
            $data['submitlabel']    = xarML('Submit');

             // dynamic properties (if any)
/*
            $data['properties'] = null;
            if (xarModIsAvailable('dynamicdata')) {
                // get the Dynamic Object defined for this module (and itemtype, if relevant)
                $object = &xarModAPIFunc('dynamicdata', 'user', 'getobject',
                    array('module' => 'roles'));
                if (isset($object) && !empty($object->objectid)) {
                    // get the Dynamic Properties of this object
                    $data['properties'] = &$object->getProperties();
                } 
            } 
*/
            break;

        case 'update':

            if (!xarVarFetch('template', 'str:1:', $template)) return;
            if (!xarVarFetch('title', 'str:1:', $title)) return; 
	        if (!xarVarFetch('usernote', 'checkbox', $usernote, false, XARVAR_NOT_REQUIRED)) return;
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            xarModSetVar('recommend', 'template', $template);
            xarModSetVar('recommend', 'title', $title);
            xarModSetVar('recommend', 'usernote', $usernote);

            xarResponseRedirect(xarModURL('recommend', 'admin', 'modifyconfig'));
            return true;

            break;
    } 

    return $data;
} 

?>