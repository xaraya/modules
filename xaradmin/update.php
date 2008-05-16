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
/**
 * This is a standard function that is called with the results of the
 * form supplied by headlines_admin_modify() to update a current item
 * @param 'hid' the id of the link to be updated
 * @param 'url' the url of the link to be updated
 */
function headlines_admin_update()
{
    $settings = array();
    // Get parameters from whatever input we need
    if (!xarVarFetch('hid','int:1:',$data['hid'])) return;
    if (!xarVarFetch('obid','str:1:',$obid,$data['hid'],XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title','str:1:',$data['title'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc','str:1:',$data['desc'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order','str:1:',$data['order'],'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('url','str:1:',$data['url'],'http://www.xaraya.com/?theme=rss',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:1:', $return_url, '', XARVAR_NOT_REQUIRED)) return;
    // per feed settings
    if (!xarVarFetch('itemsperpage', 'int:1', $settings['itemsperpage'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdescription', 'int:1', $settings['maxdescription'], 0, XARVAR_NOT_REQUIRED)) return;
    // fetch any simplepie options too
    if (!xarVarFetch('showchanimage', 'checkbox', $settings['showchanimage'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showitemimage', 'checkbox', $settings['showitemimage'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showitemcats', 'checkbox', $settings['showitemcats'], 0, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // call api function to get the parsed feed (or warning)
    $getfeed = xarModAPIFunc('headlines', 'user', 'getparsed', 
            array('feedfile' => $data['url']));

    if (!empty($getfeed['warning'])) {
        $data['warning'] = xarVarPrepForDisplay($getfeed['warning']);
        $data['module']         = 'headlines';
        $data['itemtype']       = NULL; // forum
        $data['itemid']         = $data['hid'];
        $hooks = xarModCallHooks('item','modify',$data['hid'],$data);
        if (empty($hooks)) {
            $data['hooks']      = '';
        } elseif (is_array($hooks)) {
            $data['hooks']      = join('',$hooks);
        } else {
            $data['hooks']      = $hooks;
        }
        $data['parser'] = $getfeed['parser'];
        $data['itemsperpage'] = $settings['itemsperpage'];
        $data['maxdescription'] = $settings['maxdescription'];
        $data['showchanimage'] = $settings['showchanimage'];
        $data['showitemimage'] = $settings['showitemimage'];
        $data['showitemcats'] = $settings['showitemcats'];
        $data['submitlabel']    = xarML('Submit');
        $data['authid']         = xarSecGenAuthKey();
        return xarTPLModule('headlines', 'admin', 'modify', $data);
    }
    $data['transform'] = array('desc');
    $data = xarModCallHooks('item', 'transform-input', 0, $data, 'headlines', 0);
    
    $data['settings'] = serialize($settings);

    if(!xarModAPIFunc('headlines',
                      'admin',
                      'update',
                      array('hid'   => $data['hid'],
                            'title' => $data['title'],
                            'desc'  => $data['desc'],
                            'url'   => $data['url'],
                            'order' => $data['order'],
                            'settings' => $data['settings'],
                            'string' => $getfeed['compare'],
                            'date' => $getfeed['lastitem']))) return;

    if (empty($return_url)) {
        $return_url = xarModURL('headlines', 'admin', 'view');
    }
    xarResponseRedirect($return_url);

    // Return
    return true;
}
?>