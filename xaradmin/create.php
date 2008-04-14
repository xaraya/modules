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
    if (!xarVarFetch('url','str:1:255',$url)) return;
    if (!xarVarFetch('title', 'str:1:255', $title, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc', 'str:1:255', $desc, '', XARVAR_NOT_REQUIRED)) return;
    extract($args);

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // FR: added this routine to support local urls
    $invalid = false;
    $islocal = false;
    if (empty($url)) {
        $invalid = true;
    } elseif (strstr($url,'://')) {
        if (!ereg("^http://|https://|ftp://", $url)) {            
            $invalid = true;
        }
        $server = xarServerGetHost();
        if (preg_match("!://($server|localhost|127\.0\.0\.1)(:\d+|)/!",$url)) {
            $islocal = true;
        }
    } elseif (substr($url,0,1) == '/') {
        $server = xarServerGetHost();
        $protocol = xarServerGetProtocol();
        $url = $protocol . '://' . $server . $url;
        $islocal = true;
    } else {
        $baseurl = xarServerGetBaseURL();
        $url = $baseurl . $url;
        $islocal = true;
    }

    if ($invalid) {
      $data['warning'] = xarML('Invalid Address for Feed');
    } else {
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
    }
    if (!empty($data['warning'])){
        $item = array();
        $item['module'] = 'headlines';
        $item['itemtype'] = NULL; // forum

        $hooks = xarModCallHooks('item','new','',$item);

        if (empty($hooks)) {
        $data['hooks'] = '';
        } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
        } else {
        $data['hooks'] = $hooks;
        }
        
        $data['url'] = $url;
        $data['title'] = $title;
        $data['desc'] = $desc;
        $data['submitlabel'] = xarML('Submit');
        $data['authid'] = xarSecGenAuthKey();

        return xarTPLModule('headlines', 'admin', 'new', $data);
    }
    // The API function is called
    $hid = xarModAPIFunc('headlines', 'admin', 'create', array('url' => $url, 'title' => $title, 'desc' => $desc));

    if ($hid == false) return;

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}
?>