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
    // per feed settings
    if (!xarVarFetch('itemsperpage', 'int:1', $settings['itemsperpage'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdescription', 'int:1', $settings['maxdescription'], 0, XARVAR_NOT_REQUIRED)) return;
    // fetch any simplepie options too
    if (!xarVarFetch('showchanimage', 'checkbox', $settings['showchanimage'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showitemimage', 'checkbox', $settings['showitemimage'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showitemcats', 'checkbox', $settings['showitemcats'], 0, XARVAR_NOT_REQUIRED)) return;
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
        $data['itemsperpage'] = $settings['itemsperpage'];
        $data['maxdescription'] = $settings['maxdescription'];
        $data['showchanimage'] = $settings['showchanimage'];
        $data['showitemimage'] = $settings['showitemimage'];
        $data['showitemcats'] = $settings['showitemcats'];        
        $data['url'] = $url;
        $data['title'] = $title;
        $data['desc'] = $desc;
        $data['submitlabel'] = xarML('Submit');
        $data['authid'] = xarSecGenAuthKey();

        return xarTPLModule('headlines', 'admin', 'new', $data);
    }
    $data['settings'] = serialize($settings);
    $data['compare'] = !isset($data['compare']) ? '' : $data['compare'];
    // set the date to the time of the last item if we have it, otherwise, set it to now
    $date = !empty($data['lastitem']) ? $data['lastitem'] : time();
    // The API function is called
    $hid = xarModAPIFunc('headlines', 'admin', 'create', array('url' => $url, 'title' => $title, 'desc' => $desc, 'settings' => $data['settings'], 'string' => $data['compare'], 'date' => $date));

    if ($hid == false) return;
    
    if (empty($return_url)) {
        $return_url = xarModURL('headlines', 'admin', 'view');
    }
    xarResponseRedirect($return_url);

    // Return
    return true;
}
?>