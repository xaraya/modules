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
    if (!xarVarFetch('return_url', 'str:1:', $return_url, '', XARVAR_NOT_REQUIRED)) return;
    extract($args);

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // call api function to get the parsed feed (or warning)
    $data = xarModAPIFunc('headlines', 'user', 'getparsed', 
            array('feedfile' => $url));

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
    
    if (empty($return_url)) {
        $return_url = xarModURL('headlines', 'admin', 'view');
    }
    xarResponseRedirect($return_url);

    // Return
    return true;
}
?>