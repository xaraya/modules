<?php
/**
 * Create a new external course
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Create a new external course, itsp course
 *
 * Standard function to create a new plan
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('itsp','admin','new') to create a new item
 *
 * @author michelv <michelv@xarayahosting.nl>
 * @since 21 Feb 2006
 * @param  int credits
 * @param  int mincredit
 * @param string dateopen
 * @todo michelv <1> implement correct return url
 * @return bool true on succes with redirect URL
 */
function itsp_admin_create_icourse($args)
{
    extract($args);

    // The general parameters
    if (!xarVarFetch('itspid',   'id', $itspid )) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    // The external course data
    if (!xarVarFetch('icourseid',      'id',        $icourseid,      '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icoursetitle',   'str:1:255', $icoursetitle,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icourseloc',     'str:1:255', $icourseloc,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icoursedesc',    'str::',     $icoursedesc,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icoursecredits', 'int::',     $icoursecredits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icourselevel',   'str:1:255', $icourselevel,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icourseresult',  'str:1:255', $icourseresult,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('icoursedate',    'str::',     $icoursedate,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateappr',       'str::',     $dateappr,       '', XARVAR_NOT_REQUIRED)) return;
    // Invalid check
    if (!xarVarFetch('invalid', 'array',  $invalid, array(), XARVAR_NOT_REQUIRED)) return;

    /* Argument check. See if this one has already been entered

    $item = xarModAPIFunc('itsp',
                          'user',
                          'validateitem',
                          array('name' => $name));
     */
    // Argument check
    $invalid = array();
    if (empty($icoursecredits) || !is_numeric($icoursecredits)) {
        $invalid['icoursecredits'] = 1;
        $icourse = '';
    }
    if (empty($icoursetitle) || !is_string($icoursetitle)) {
        $invalid['icoursetitle'] = 1;
        $icoursetitle = '';
    }
/*
    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicate'] = 1;
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        return xarModFunc('itsp', 'admin', 'new',
                          array('invalid' => $invalid,
                                'planname' => $planname,
                                'plandesc' => $plandesc,
                                'planrules' => $planrules,
                                'credits' => $credits,
                                'mincredit' => $mincredit,
                                'dateopen' => $dateopen,
                                'dateclose' => $dateclose));
    }
*/
     /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;
    /* The API function is called. */
    $icourseid = xarModAPIFunc('itsp',
                              'admin',
                              'create_icourse',
                              array('itspid' => $itspid,
                                    'pitemid'=> $pitemid,
                                    'icourseid'=> $icourseid,
                                    'icoursetitle'=>$icoursetitle,
                                    'icourseloc'=> $icourseloc,
                                    'icoursedesc'=>  $icoursedesc,
                                    'icoursecredits'=> $icoursecredits,
                                    'icourselevel'=> $icourselevel,
                                    'icourseresult'=> $icourseresult,
                                    'icoursedate'=>  $icoursedate,
                                    'dateappr'=>  $dateappr));
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted.
     */
    if (!isset($icourseid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    xarSessionSetVar('statusmsg', xarML('The external course was successfully created!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'user', 'modify', array('itspid' => $itspid, 'pitemid' => $pitemid)));
    /* Return true, in this case */
    return true;
}
?>
