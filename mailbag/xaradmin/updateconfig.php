<?php
/**
   This is a standard function to update the configuration parameters of the
   module given the information passed back by the modification form
*/
function mailbag_admin_updateconfig()
{
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (xarModGetVar('mailbag', 'EnforceAuthKey')){
        if (!xarSecConfirmAuthKey()) {            
            return false;
        }
    }

    if (!xarVarFetch('itemtype', 'int', $itemtype, 0, XARVAR_NOT_REQUIRED)) return;

    xarVarFetch('bgswitch',      'isset', $bgswitch, '');
    xarVarFetch('exepassword',   'str',   $exepassword, '');
    xarVarFetch('popserver',     'str',   $popserver, '');
    xarVarFetch('popuser',       'str',   $popuser, '');
    xarVarFetch('poppass',       'str',   $poppass, '');
    xarVarFetch('exepassword',   'str',   $exepassword, '');
    xarVarFetch('emaildomain',   'str',   $emaildomain, '');
    xarVarFetch('postmaster',    'str',   $postmaster, '');
    xarVarFetch('allowhtml',     'isset', $allowhtml, '');
    xarVarFetch('maxrecip',      'str',   $maxrecip, '');
    xarVarFetch('maxsize',       'str',   $maxsize, '');
    xarVarFetch('notifyuser',    'isset', $notifyuser, '');

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    // These two lines may be needed when images are added to the Help Desk
    xarModSetVar('mailbag', 'bgswitch', $bgswitch);
    xarModSetVar('mailbag', 'exepassword', $exepassword);
    xarModSetVar('mailbag', 'popserver', $popserver);
    xarModSetVar('mailbag', 'popuser', $popuser);
    xarModSetVar('mailbag', 'poppass', $poppass);
    xarModSetVar('mailbag', 'emaildomain', $emaildomain);
    xarModSetVar('mailbag', 'postmaster', $postmaster);
    xarModSetVar('mailbag', 'allowhtml', $allowhtml);
    xarModSetVar('mailbag', 'maxrecip', $maxrecip);
    xarModSetVar('mailbag', 'maxsize', $maxsize);
    xarModSetVar('mailbag', 'faqaddr', 'faq');
    xarModSetVar('mailbag', 'unknowntolog', 0);
    //xarModSetVar('mailbag', 'lastrunlog', "No run yet");
    xarModSetVar('mailbag', 'notifyuser', $notifyuser);
    xarModSetVar('mailbag', 'senderemail', 1);
            
    xarModCallHooks('module','updateconfig','mailbag',
                    array('module'   => 'mailbag',
                          'itemtype' => $itemtype)
		    ); 
    
    xarResponseRedirect(xarModURL('mailbag', 'admin', 'modifyconfig'));

    //Return
    return true;
}
?>
