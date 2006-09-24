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
function headlines_user_view()
{
    // Security Check
    if (!xarSecurityCheck('ReadHeadlines')) return;
    if (!xarVarFetch('hid', 'id', $hid)) return;

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));
    if (empty($links)) return;

    // Check and see if a feed has been supplied to us.
    if (empty($links['url'])) {
        xarResponseRedirect(xarModURL('headlines', 'user', 'main'));
        return true;
    }
    $feedfile = $links['url'];
    if (xarModGetVar('headlines', 'magpie')){
        $data = xarModAPIFunc('magpie',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    } else {
        $data = xarModAPIFunc('headlines',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    }

    if (!empty($data['warning'])){
        $msg = xarML('There is a problem with this feed : #(1)', $data['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay($data['chantitle']));

    if (isset($links['catid'])) {
        $data['catid'] = $links['catid'];
    } else {
        $data['catid'] = '';
    }
    $data['hid'] = $hid;
    $data['module'] = 'headlines';
    $data['itemtype'] = 0;
    $data['itemid'] = $hid;
    $data['returnurl'] = xarModURL('headlines',
                                   'user',
                                   'view',
                                   array('hid' => $hid));
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
