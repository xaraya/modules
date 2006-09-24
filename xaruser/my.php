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
function headlines_user_my()
{
    // Security Check
    if(!xarSecurityCheck('OverviewHeadlines')) return;

    xarVarFetch('config','str',$config,0,XARVAR_NOT_REQUIRED);
    if (!empty($config)) {
        xarVarFetch('confirm','str',$confirm,'',XARVAR_NOT_REQUIRED);
        if (!empty($confirm)) {
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
            xarVarFetch('feedlist','list:id',$feedlist,array(),XARVAR_NOT_REQUIRED);
            $feeds = array();
            foreach ($feedlist as $feed) {
                if (!empty($feed) && is_numeric($feed)) {
                    $feeds[] = $feed;
                }
            }
            $feeds = serialize($feeds);
            if (xarUserIsLoggedIn()) {
                xarModSetUserVar('headlines','showfeeds',$feeds);
            } else {
                xarSessionSetVar('headlines_showfeeds',$feeds);
            }
            xarResponseRedirect(xarModURL('headlines', 'user', 'my'));
            return true;
        }
    }

    $data = array();

    $default = xarModGetVar('headlines','showfeeds');
    if (!isset($default)) {
        xarModSetVar('headlines','showfeeds','');
        $default = '';
    }
    if (xarUserIsLoggedIn()) {
        $feeds = xarModGetUserVar('headlines','showfeeds');
    } else {
        $feeds = xarSessionGetVar('headlines_showfeeds');
    }
    if (empty($feeds)) {
        $feeds = $default;
    }
    if (!empty($feeds)) {
        $feedlist = unserialize($feeds);
    } else {
        $feedlist = array();
    }

    $data['config'] = $config;
    $data['feedlist'] = $feedlist;

    $links = xarModAPIFunc('headlines', 'user', 'getall');
    if (empty($links)) return $data;

    $hid2url = array();
    foreach ($links as $id => $link) {
        $hid2url[$link['hid']] = $link['url'];
    }
    $data['links'] = $hid2url;

    return $data;
}
?>
