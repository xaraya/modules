<?php
/**
 * Create a new person
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author Michel V.
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('sigmapersonnel','admin','new') to create a new item
 *
 * @author MichelV
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function sigmapersonnel_admin_updateperson($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('personid', 'id', $personid)) return;
    if (!xarVarFetch('userid', 'int:1:', $userid, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return; // OK to stay?
    if (!xarVarFetch('pnumber', 'int:1:', $pnumber, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('persstatus', 'int:1:', $persstatus, 1,XARVAR_NOT_REQUIRED)) return; // Status of 1 is then standard!
    if (!xarVarFetch('firstname', 'str:1:', $firstname, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastname', 'str:1:', $lastname, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tussenvgsl', 'str:1:', $tussenvgsl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('initials', 'str:1:', $initials, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sex', 'str:1:', $sex, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('street', 'str:1:100', $street, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('zip', 'str:1:100', $zip, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cityid', 'int:1:', $cityid, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phonehome', 'str:1:100', $phonehome, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mobile', 'str:1:100', $mobile, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phonework', 'str:1:100', $phonework, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email', 'str:1:100', $email, '', XARVAR_NOT_REQUIRED)) return; //Type email?
    if (!xarVarFetch('privphonehome', 'checkbox', $privphonehome, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privwork', 'checkbox', $privwork,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privemail', 'checkbox', $privemail, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privbirthdate', 'checkbox', $privbirthdate, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privaddress', 'checkbox', $privaddress, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privphonework', 'checkbox', $privphonework, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactname', 'str:1:100', $contactname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactphone', 'str:1:100', $contactphone, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactstreet', 'str:1:100', $contactstreet, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactcityid', 'int:1:', $contactcityid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactrelation', 'str:1:100', $contactrelation, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactmobile', 'str:1:100', $contactmobile, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('birthdate', 'str::', $birthdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('birthplace', 'str:1:100', $birthplace, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nrkdistrict', 'int:1:', $nrkdistrict, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nrknumber', 'int:1:', $nrknumber, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehbonr', 'int:1:', $ehbonr, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehboplus', 'checkbox', $ehboplus, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehbodate', 'str:1:100', $ehbodate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehboplace', 'str:1:100', $ehboplace, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateintake', 'str:1:100', $dateintake, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('intakeby', 'str:1:100', $intakeby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateemploy', 'str:1:100', $dateemploy, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateout', 'str:1:100', $dateout, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateouttalk', 'str:1:100', $dateouttalk, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('outreason', 'str:1:100', $outreason, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('outtalkwith', 'str:1:100', $outtalkwith, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateshoes', 'str:1:100', $dateshoes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sizeshoes', 'int:1:2', $sizeshoes, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('banknr', 'str:1:15', $banknr, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bankplaceid', 'int:1:', $bankplaceid, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateshoes', 'str:1:100', $dateshoes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('others', 'str::', $others, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('educationremarks', 'str::', $educationremarks, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array', $invalid, array(), XARVAR_NOT_REQUIRED)) return;
    // Argument check
    $item = xarModAPIFunc('sigmapersonnel',
                          'user',
                          'get',
                          array('personid' => $personid));

    // Argument check
    $invalid = array();
    if (empty($pnumber) || !is_numeric($pnumber)) {
        $invalid['pnumber'] = 1;
        $number = '';
    }
    if (empty($firstname) || !is_string($firstname)) {
        $invalid['firstname'] = 1;
        $firstname = '';
    }
    if (empty($lastname) || !is_string($lastname)) {
        $invalid['lastname'] = 1;
        $lastname = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('sigmapersonnel', 'admin', 'modifyperson',
                          array(
                            'personid' => $personid,
                            'userid' => $userid,
                            'pnumber' => $pnumber,
                            'persstatus' => $persstatus,
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                            'tussenvgsl' => $tussenvgsl,
                            'initials' => $initials,
                            'sex' => $sex,
                            'title' => $title,
                            'street' => $street,
                            'zip' => $zip,
                            'cityid' => $cityid,
                            'phonehome' => $phonehome,
                            'mobile' => $mobile,
                            'phonework' => $phonework,
                            'email' => $email,
                            'privphonehome' => $privphonehome,
                            'privwork' => $privwork,
                            'privemail' => $privemail,
                            'privbirthdate' => $privbirthdate,
                            'privaddress' => $privaddress,
                            'privphonework' => $privphonework,
                            'contactname' => $contactname,
                            'contactphone' => $contactphone,
                            'contactstreet' => $contactstreet,
                            'contactcityid' => $contactcityid,
                            'contactrelation' => $contactrelation,
                            'contactmobile' => $contactmobile,
                            'birthdate' => $birthdate,
                            'birthplace' => $birthplace,
                            'nrkdistrict' => $nrkdistrict,
                            'nrknumber' => $nrknumber,
                            'ehbonr' => $ehbonr,
                            'ehboplus' => $ehboplus,
                            'ehbodate' => $ehbodate,
                            'ehboplace' => $ehboplace,
                            'dateintake' => $dateintake,
                            'intakeby' => $intakeby,
                            'dateemploy' => $dateemploy,
                            'dateout' => $dateout,
                            'dateouttalk' => $dateouttalk,
                            'outreason' => $outreason,
                            'outtalkwith' => $outtalkwith,
                            'dateshoes' => $dateshoes,
                            'sizeshoes' => $sizeshoes,
                            'banknr' => $banknr,
                            'bankplaceid' => $bankplaceid,
                            'others' => $others,
                            'educationremarks' => $educationremarks,
                            'invalid' => $invalid));
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // The API function is called.
    $personid = xarModAPIFunc('sigmapersonnel',
                          'admin',
                          'updateperson',
                          array('personid' => $personid,
                            'userid' => $userid,
                            'pnumber' => $pnumber,
                            'persstatus' => $persstatus,
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                            'tussenvgsl' => $tussenvgsl,
                            'initials' => $initials,
                            'sex' => $sex,
                            'title' => $title,
                            'street' => $street,
                            'zip' => $zip,
                            'cityid' => $cityid,
                            'phonehome' => $phonehome,
                            'mobile' => $mobile,
                            'phonework' => $phonework,
                            'email' => $email,
                            'privphonehome' => $privphonehome,
                            'privwork' => $privwork,
                            'privemail' => $privemail,
                            'privbirthdate' => $privbirthdate,
                            'privaddress' => $privaddress,
                            'privphonework' => $privphonework,
                            'contactname' => $contactname,
                            'contactphone' => $contactphone,
                            'contactstreet' => $contactstreet,
                            'contactcityid' => $contactcityid,
                            'contactrelation' => $contactrelation,
                            'contactmobile' => $contactmobile,
                            'birthdate' => $birthdate,
                            'birthplace' => $birthplace,
                            'nrkdistrict' => $nrkdistrict,
                            'nrknumber' => $nrknumber,
                            'ehbonr' => $ehbonr,
                            'ehboplus' => $ehboplus,
                            'ehbodate' => $ehbodate,
                            'ehboplace' => $ehboplace,
                            'dateintake' => $dateintake,
                            'intakeby' => $intakeby,
                            'dateemploy' => $dateemploy,
                            'dateout' => $dateout,
                            'dateouttalk' => $dateouttalk,
                            'outreason' => $outreason,
                            'outtalkwith' => $outtalkwith,
                            'dateshoes' => $dateshoes,
                            'sizeshoes' => $sizeshoes,
                            'banknr' => $banknr,
                            'bankplaceid' => $bankplaceid,
                            'others' => $others,
                            'educationremarks' => $educationremarks));

    // The return value of the function is checked here
    if (!isset($personid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    xarSessionSetVar('statusmsg', xarML('Person successfully updated!'));
    xarResponseRedirect(xarModURL('sigmapersonnel', 'admin', 'viewpersons'));
    // Return
    return true;
}

?>
