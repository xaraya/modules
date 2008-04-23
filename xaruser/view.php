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

function headlines_user_view($args)
{

    // Security Check
    if (!xarSecurityCheck('ReadHeadlines')) return;
	if (!xarVarFetch('hid', 'id', $hid, NULL, XARVAR_NOT_REQUIRED)) return;
    // TODO: optional force cache refresh, admin only option
    if (!xarVarFetch('renew', 'isset', $renew, 0, XARVAR_NOT_REQUIRED)) return; 
	extract($args);	
	if (!isset($hid) || empty($hid) || !is_numeric($hid)) {
        $msg = xarML('No headline id specified.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // The user API function is called
    $links = xarModAPIFunc('headlines', 'user', 'get', array('hid' => $hid));
    if (empty($links)) return;

    // Check and see if a feed has been supplied to us.
    if (empty($links['url'])) {
        xarResponseRedirect(xarModURL('headlines', 'user', 'main'));
        return true;
    }
    
    $settings = array();
    // TODO: Get the settings for this feed
    $setstring = isset($links['settings']) ? $links['settings'] : array();
    if (!empty($setstring)) {
        if (is_string($setstring)) {
            $settings = unserialize($setstring);
        } elseif (is_array($setstring)) {
            $settings = $setstring;
        }
    } 
    // set params based on feed settings falling back to module defaults if none found
    $numitems = isset($settings['numitems']) ? $settings['numitems'] : xarModGetVar('headlines', 'feeditemsperpage');
    $truncate = isset($settings['truncate']) ? $settings['truncate'] : xarModGetVar('headlines', 'maxdescription'); 
    $refresh = isset($settings['refresh']) ? $settings['refresh'] : 3600;
    // TODO: admin only force cache refresh option
    if ($renew) {
        $refresh = '0';
    }

    $feedfile = $links['url'];

    // call api function to get the parsed feed (or warning)
    $data = xarModAPIFunc('headlines', 'user', 'getparsed', 
        array('feedfile' => $feedfile, 'numitems' => $numitems, 'truncate' => $truncate, 'refresh' => $refresh));

    if (!empty($data['warning'])){
        // don't throw exception, let the display handle this
        $data['chantitle'] = xarML('Feed unavailable');
        $data['chandesc'] = $data['warning'];
        $data['chanlink'] = '#';
    }
    if (!empty($links['title'])){ // optionally over-rides title with alt_title
        $data['chantitle'] = $links['title']; 
    }
    if (!empty($links['desc'])){  // optionally over-rides description with alt_description
        $data['chandesc'] = $links['desc'];
    }

    xarTplSetPageTitle($data['chantitle']);
    
    if (isset($links['catid'])) {
        $data['catid'] = $links['catid'];
    } else {
        $data['catid'] = '';
    }
    $data['hid'] = $hid;
    $data['module'] = 'headlines';
    $data['itemtype'] = 0;
    $data['itemid'] = $hid;
    $data['returnurl'] = xarModURL('headlines', 'user', 'view', array('hid' => $hid));
    $hooks = xarModCallHooks('item', 'display', $hid, $data);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // only generate authid when the user is allowed to import
    $importpubtype = xarModGetVar('headlines','importpubtype');
    if (!empty($importpubtype) && xarSecurityCheck('EditHeadlines', 0)) {
        $data['authid'] = xarSecGenAuthKey();
    } else {
        $data['authid'] = '';
    }

    return $data;
}

?>