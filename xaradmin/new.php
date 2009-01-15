<?php
/**
 * Create a new item of the mailer object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mailer_admin_new()
    {
        if (!xarSecurityCheck('AddMailer')) return;

        if (!xarVarFetch('name',       'str',    $name,            'mailer_mailer', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'mailer';
        $data['authid'] = xarSecGenAuthKey('mailer');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSecConfirmAuthKey()) return;
            
            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('mailer','user','new', $data);        
            } else {
                // Good data: create the item
                $item = $data['object']->createItem();
                
                // Jump to the next page
                xarResponseRedirect(xarModURL('mailer','admin','view'));
                return true;
            }
        }
        return $data;
    }
?>