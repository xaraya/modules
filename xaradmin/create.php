<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
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
    $hid = xarModAPIFunc('headlines', 'admin', 'create', array('url' => $url));

    if ($hid == false) return;

    // Lets Create the Cache Right now to save processing later.

    // TODO: This check is done in several places now. It should be hidden in an API.
    // TODO: Also need to check that these parser modules have not been disabled or uninstalled.
    if (xarModGetVar('headlines', 'parser') == 'simplepie') {
        // Use the SimplePie parser
        $data = xarModAPIFunc(
            'simplepie', 'user', 'process',
            array('feedfile' => $url)
        );
    } elseif (xarModGetVar('headlines', 'magpie') || xarModGetVar('headlines', 'parser') == 'magpie') {
        $data = xarModAPIFunc(
            'magpie', 'user', 'process',
            array('feedfile' => $url)
        );
    } else {
        $data = xarModAPIFunc(
            'headlines', 'user', 'process',
            array('feedfile' => $url)
        );
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