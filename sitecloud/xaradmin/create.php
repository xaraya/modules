<?php
function sitecloud_admin_create()
{
	if(!xarSecurityCheck('Addsitecloud')) return;
    if (!xarVarFetch('url', 'str:1:', $url, 'http://www.xaraya.com')) return; 
    if (!xarVarFetch('title', 'str:1:', $title, 'Xaraya')) return; 
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    if (!ereg("^http://|https://|ftp://", $url)) {
        $msg = xarML('Invalid Address for Cloud to Monitor');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // The API function is called
    $id = xarModAPIFunc('sitecloud',
                        'admin',
                        'create',
                        array('url'     => $url,
                              'title'   => $title));

    if ($id == false) return;   
    xarResponseRedirect(xarModURL('sitecloud', 'admin', 'view'));
    // Return
    return true;
}
?>
