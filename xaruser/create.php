<?php
/**
 * Standard function to create a new item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel Module Development Team
 */

/**
 * Create a new absence
 *
 * @param  $ 'start' the startdate
 * @param  $ 'end' the enddate
 */
function sigmapersonnel_user_create($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('personid', 'id', $personid, '')) return;
    if (!xarVarFetch('start',    'str:1:', $start, $start, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end',      'str:1:', $end, $end, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('typeid',   'id', $typeid, '', XARVAR_NOT_REQUIRED)) return;

    // Argument check
    // TODO Make sure there is not already one present in this time frame
    /*
    $item = xarModAPIFunc('sigmapersonnel',
                          'user',
                          'validateitem',
                          array('start' => $start), 'end' => $end);
    */
    // Argument check
    $invalid = array();
    if (empty($start) || !is_string($start)) {
        //echo "start";
        $invalid['start'] = 1;
        $number = '';
    }
    if (empty($end) || !is_string($end)) {
        //echo "end";
        $invalid['end'] = 1;
        $name = '';
    }
    if (empty($typeid) || !is_integer($typeid)) {
        //echo "typeid";
        // This is weird
        $invalid['typeid'] = 1;
        $typeid = 0;
    }
    /*
    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicatename'] = 1;
        $duplicatename = '';
    }
    */
    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        return xarModFunc('sigmapersonnel', 'user', 'new',
                          array('start' => $start,
                                'end'   => $end,
                                'invalid' => $invalid));
    }
    if (!xarSecConfirmAuthKey()) return;
    $uid = xarUserGetVar('uid');
    if (empty($personid)) {
        $person = xarModAPIFunc('sigmapersonnel','user', 'getpersonid', array('uid'=>$uid));
        $personid = $person['personid'];
    }
    $pid = xarModAPIFunc('sigmapersonnel',
                          'user',
                          'create',
                          array('start'     => strtotime($start),
                                'end'       => strtotime($end),
                                'typeid'    => $typeid,
                                'personid'  => $personid));

    if (!isset($pid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('sigmapersonnel', 'user', 'main'));
    // Return
    return true;
}
?>