<?php
/**
 * Standard function to update a current item
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
 * Update an itsp
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('itsp','user','modify') to update an itsp. It sorts out what
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  int 'itspid' the id of the item to be updated
 * @param  int 'objectid' universal identifier OPTIONAL
 * @param  int pitemid the number of the plan item to be updated
 * @param array invalid
 * @since 20 feb 2006
 * @todo michelv: <1>why doesn't the sec check in here work?
 *                <2> Set the correct return URL
 */
function itsp_user_update()
{
    //extract($args);

    if (!xarVarFetch('itspid',   'id',    $itspid,   $itspid,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',    $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id',    $pitemid,  $pitemid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid,  array(),   XARVAR_NOT_REQUIRED)) return;
   // if (!xarVarFetch('authid',   'str::', $authid,  '',  XARVAR_NOT_REQUIRED)) return;
    if (!empty($objectid)) {
        $pitemid = $objectid;
    }
 //   if (!xarSecConfirmAuthKey()) return;
    // TODO: include check here for passing variables.
    //   $itspid = $itsp['itspid'];
    // The user API function is called to get the ITSP
    $itsp = xarModAPIFunc('itsp',
                          'user',
                          'get',
                          array('itspid' => $itspid));
    $planid = $itsp['planid'];
    /* Security check
     */
    if (!xarSecurityCheck('ReadITSP', 1, 'ITSP', "$itspid:$planid")) {
        return;
    }

    // Check to see if we are already dealing with a planitem
    if (!empty($pitemid) && is_numeric($pitemid)) {
        //get planitem
        $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
        $data['pitemrules'] = $pitem['pitemrules'];
        // Splice the rule
        if (!empty($pitem['pitemrules'])) {
            list($Rtype, $Rlevel, $Rcat, $Rsource) = explode(";", $pitem['pitemrules']);

            $rule_parts = explode(':',$Rtype);
            $rule_type = $rule_parts[1];
            $rule_parts = explode(':',$Rlevel);
            $rule_level = $rule_parts[1];
            $rule_parts = explode(':',$Rcat);
            $rule_cat = $rule_parts[1];
            $rule_parts = explode(':',$Rsource);
            $rule_source = $rule_parts[1];

            $data['rule_type'] = $rule_type;
            $data['rule_level'] = $rule_level;
            $data['rule_cat'] = $rule_cat;
            $data['rule_source'] = $rule_source;

        }
        switch ($rule_source) {
            case 'courses':
                // Then we are adding a course, if this id is set
                if (!xarVarFetch('lcourseid', 'id',    $lcourseid, '', XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('dateappr',  'str::', $dateappr,  '', XARVAR_NOT_REQUIRED)) return;
                // Make sure we will not add the empty string as a course
                if (!empty($lcourseid) && $lcourseid > 0) {
                    // Create a new linked course
                    if (!xarModApiFunc('itsp','admin','create_courselink',
                                        array('itspid' =>$itspid,
                                              'pitemid' => $pitemid,
                                              'lcourseid' => $lcourseid,
                                              'dateappr' => $dateappr)
                                        )) {
                        return;
                    }
                }
                xarSessionSetVar('statusmsg', xarML('Course Item was successfully added!'));
                break;
            default:
                if (!xarVarFetch('icourseid',   'id',    $icourseid, '',   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursetitle', 'str:1:255',    $icoursetitle, '', XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icourseloc',  'str:1:255',    $icourseloc, '',  XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursedesc',   'str::',    $icoursedesc, '',   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursecredits',   'int::',    $icoursecredits, '',   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icourselevel', 'str:1:255',    $icourselevel, '', XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icourseresult',  'str:1:255',    $icourseresult, '',  XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursedate',   'str::',    $icoursedate, '',   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('dateappr',   'str::',    $dateappr, '',   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('invalid',  'array', $invalid,  array(),   XARVAR_NOT_REQUIRED)) return;

                if (!xarModFunc('itsp',
                                   'admin',
                                   'create_icourse',
                                   array(
                                   'itspid' => $itspid,
                                   'pitemid' => $pitemid,
                                   'icourseid'=>$icourseid,
                                   'icoursetitle'=> $icoursetitle,
                                   'icourseloc'=>  $icourseloc,
                                   'icoursedesc'=> $icoursedesc,
                                   'icoursecredits'=>  $icoursecredits,
                                   'icourselevel'=> $icourselevel,
                                   'icourseresult'=> $icourseresult,
                                   'icoursedate'=> $icoursedate,
                                   'dateappr'=> $dateappr,
                                   'authid' => xarSecGenAuthKey('itsp')
                                         )
                                )) {
                    return; /* throw back */
                }
                xarSessionSetVar('statusmsg', xarML('ITSP Item was successfully updated!'));
        }
    }


/*
    $invalid = array();
    if (empty($number) || !is_numeric($number)) {
        $invalid['number'] = 1;
        $number = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
        $name = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the user_new function and return the template vars

        return xarModFunc('itsp', 'user', 'modify',
                          array('name'     => $name,
                                'number'   => $number,
                                'invalid'  => $invalid));
    }
*/
  //  xarSessionSetVar('statusmsg', xarML('ITSP Item was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'user', 'modify', array('itpsid'=>$itspid, 'pitemid'=> $pitemid)));
    /* Return */
    return true;
}
?>