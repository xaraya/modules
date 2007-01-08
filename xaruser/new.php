<?php
/**
 * Standard function to create a new module item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author SIGMApersonnel module development team
 */
/**
 * Add new presence item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author MichelV michelv@xarayahosting.nl
 *
 * @param start str startdate of the presenceitem
 * @param end str enddate of this presenceitem
 * @param uid id The userid of the person. This can be the current person, or the
 * @return array
 */
function sigmapersonnel_user_new($args)
{
    extract($args);

    if (!xarVarFetch('start', 'str:1:', $start, $start,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end', 'str:1:', $end, $end, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('sigmapersonnel', 'user', 'menu');
    // Security check
    if (!xarSecurityCheck('AddSIGMAPresence')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;
    // Get the presencetypes
    // TODO: what if there are no types defined?
    $data['types'] = xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                      array('itemtype' => 5));
    // Pass the id of the person this user is in SIGMA terms.
    $personid = xarModAPIFunc('sigmapersonnel', 'user', 'getpersonid',
                                      array('uid' => xarUserGetVar('uid')));
    if (!empty($personid)) {
        $data['personid'] = $personid['personid'];
    } else {
        xarSessionSetVar('statusmsg', xarML('You are not known in the personnel database. Please contact the administration!'));
        return xarTplModule('sigmapersonnel', 'user', 'main', $data);; // TODO: add nice goodbye
    }
    // get the entered presenceitems for this person to display
    $presitems = xarModApiFunc('sigmapersonnel','user','getallpresence',array('personid'=>$data['personid']));
    $data['presitems'] = $presitems;

    $item = array();
    $item['module'] = 'sigmapersonnel';
    $item['itemtype'] = 2;
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors
    if (empty($start)) {
        $data['start'] = '';
    } else {
        $data['start'] = $start;
    }

    if (empty($end)) {
        $data['end'] = '';
    } else {
        $data['end'] = $end;
    }
    // Return the template variables defined in this function
    return $data;
}
?>