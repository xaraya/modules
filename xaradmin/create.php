<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @author John Cox
*/
function headlines_admin_create($args)
{
    if (!xarVarFetch('url','str:1:',$url)) return;
    extract($args);

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

      if (!ereg("^http://|https://|ftp://", $url)) {
        $msg = xarML('Invalid Address for Feed');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
      }

    // The API function is called
    $hid = xarModAPIFunc('headlines',
                         'admin',
                         'create',
                         array('url' => $url));

    if ($hid == false) return;   

    // Lets Create the Cache Right now to save processing later.

    if (xarModGetVar('headlines', 'magpie')){
        $data = xarModAPIFunc('magpie',
                              'user',
                              'process',
                              array('feedfile' => $url));
    } else {
        $data = xarModAPIFunc('headlines',
                              'user',
                              'process',
                              array('feedfile' => $url));
    }

    if (!empty($data['warning'])){
        $msg = xarML('There is a problem with this feed : #(1)', $info['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));
    // Return
    return true;
}
?>