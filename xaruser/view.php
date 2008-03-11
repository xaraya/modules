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
    $feedfile = $links['url'];

    // TODO: This check is done in several places now. It should be hidden in an API.
    // TODO: Also need to check that these parser modules have not been disabled or uninstalled.
    // TODO: Would be nice if we fell through to the default parser, if above check fails.
    if (xarModGetVar('headlines', 'parser') == 'simplepie') {
        $data = xarModAPIFunc(
            'simplepie', 'user', 'process',
            array('feedfile' => $feedfile)
        );
    } elseif (xarModGetVar('headlines', 'magpie') || xarModGetVar('headlines', 'parser') == 'magpie') {
        $data = xarModAPIFunc(
            'magpie', 'user', 'process',
            array('feedfile' => $feedfile)
        );
    } else {
        $data = xarModAPIFunc(
            'headlines', 'user', 'process',
            array('feedfile' => $feedfile)
        );
    }

    if (!empty($data['warning'])){
        // don't throw exception, let the display handle this
        $data['chantitle'] = xarML('Feed unavailable');
        $data['chandesc'] = xarML('There is a problem with this feed');
        $data['chanlink'] = '#';
        /*
        $msg = xarML('There is a problem with this feed : #(1)', $data['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
        */
    }
        if (!empty($links['title'])){
            $data['chantitle'] = $links['title'];
        }
        if (!empty($links['desc'])){
            $data['chandesc'] = $links['desc'];
        }
    $numitems = xarModGetVar('headlines', 'feeditemsperpage');
    if (!empty($numitems)) {
	    // trim the array to just the items we were asked for 
	    $data['feedcontent'] = array_slice($data['feedcontent'], 0, $numitems);
    }
    xarTplSetPageTitle(xarVarPrepForDisplay($data['chantitle']));

	/* optionally shorten descriptions */
    $maxdesc = xarModGetVar('headlines', 'maxdescription');
	if (!empty($maxdesc)) {
		for ($i=0; $i < count($data['feedcontent']) ; $i++) {
			// only transfrom descriptions longer than specified max
			if (!empty($data['feedcontent'][$i]['description']) && (strlen($data['feedcontent'][$i]['description']) > $maxdesc)) {
				$data['feedcontent'][$i]['description'] = substr($data['feedcontent'][$i]['description'], 0, $maxdesc).'...';
			}
		}
	}

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