<?php

/**
 * the main user function
 * 
 * @author mikespub
 * @access public 
 * @param no $ parameters
 * @return true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function workflow_user_main()
{ 
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow')) return;

    xarVarFetch('iid','isset',$iid,'',XARVAR_NOT_REQUIRED);
    xarVarFetch('return_url','isset',$return_url,'',XARVAR_NOT_REQUIRED);

    if (xarUserIsLoggedIn() && !empty($iid)) {
        $seenlist = xarModGetUserVar('workflow','seenlist');
        if (empty($seenlist)) {
            xarModSetUserVar('workflow','seenlist',$iid);
        } else {
            xarModSetUserVar('workflow','seenlist',$seenlist.';'.$iid);
        }
        if (!empty($return_url)) {
            xarResponseRedirect($return_url);
            return true;
        }
    }

    // Return the output
    return array();
} 

?>
