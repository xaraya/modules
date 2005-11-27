<?php
/**
 * Standard function to modify an item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @param  id personid the id of the person to be modified
 */
function sigmapersonnel_admin_modifyperson($args)
{
    extract($args);
    // Get parameters from whatever input we need.
    if (!xarVarFetch('personid', 'id', $personid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid', 'int:1:', $userid, $userid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pnumber', 'int:1:', $pnumber, $pnumber,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('persstatus', 'int:1:', $persstatus, $persstatus,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('firstname', 'str:1:', $firstname, $firstname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastname', 'str:1:', $lastname, $lastname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tussenvgsl', 'str:1:', $tussenvgsl, $tussenvgsl, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('initials', 'str:1:', $initials, $initials, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sex', 'str:1:', $sex, $sex, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, $title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('street', 'str:1:100', $street, $street, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('zip', 'str:1:100', $zip, $zip, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cityid', 'int:1:', $cityid, $cityid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phonehome', 'str:1:100', $phonehome, $phonehome, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mobile', 'str:1:100', $mobile, $mobile, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phonework', 'str:1:100', $phonework, $phonework, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email', 'str:1:100', $email, $email, XARVAR_NOT_REQUIRED)) return; //Type email?
    if (!xarVarFetch('privphonehome', 'int:1:', $privphonehome, $privphonehome,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privwork', 'int:1:', $privwork, $privwork,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privemail', 'int:1:', $privemail, $privemail,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privbirthdate', 'int:1:', $privbirthdate, $privbirthdate,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privaddress', 'int:1:', $privaddress, $privaddress,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('privphonework', 'int:1:', $privphonework, $privphonework,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactname', 'str:1:100', $contactname, $contactname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactphone', 'str:1:100', $contactphone, $contactphone, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactstreet', 'str:1:100', $contactstreet, $contactstreet, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactcityid', 'int:1:', $contactcityid, $contactcityid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactrelation', 'str:1:100', $contactrelation, $contactrelation, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactmobile', 'str:1:100', $contactmobile, $contactmobile, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('birthdate', 'str', $birthdate, $birthdate, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('birthplace', 'str:1:100', $birthplace, $birthplace, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nrkdistrict', 'int:1:', $nrkdistrict, $nrkdistrict,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nrknumber', 'int:1:', $nrknumber, $nrknumber,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehbonr', 'int:1:', $ehbonr, $ehbonr,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehboplus', 'int:1:', $ehboplus, $ehboplus,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehbodate', 'str:1:100', $ehbodate, $ehbodate, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ehboplace', 'str:1:100', $ehboplace, $ehboplace, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateintake', 'str:1:100', $dateintake, $dateintake, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('intakeby', 'str:1:100', $intakeby, $intakeby, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateemploy', 'str:1:100', $dateemploy, $dateemploy, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateout', 'str:1:100', $dateout, $dateout, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateouttalk', 'str:1:100', $dateouttalk, $dateouttalk, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('outreason', 'str:1:100', $outreason, $outreason, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('outtalkwith', 'str:1:100', $outtalkwith, $outtalkwith, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateshoes', 'str:1:100', $dateshoes, $dateshoes, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sizeshoes', 'int:1:2', $sizeshoes, $sizeshoes,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('banknr', 'str:1:15', $banknr, $banknr,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bankplaceid', 'int:1:', $bankplaceid, $bankplaceid,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('others', 'str::', $others, $others, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('educationremarks', 'str::', $educationremarks, $educationremarks, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastmodified', 'str:1:25', $lastmodified, $lastmodified,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastmodifiedby', 'int:1:', $lastmodifiedby, $lastmodifiedby,XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $personid = $objectid;
    }
    // The user API function is called.
    $item = xarModAPIFunc('sigmapersonnel',
                          'user',
                          'get',
                          array('personid' => $personid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:$item[persstatus]")) { // add catid:
        return;
    }

    $data['useridlabel'] = xarVarPrepForDisplay(xarML('Xaraya User ID '));
    $data['pnumberlabel'] = xarVarPrepForDisplay(xarML('SIGMA number '));
    $data['persstatuslabel'] = xarVarPrepForDisplay(xarML('Status '));
    $data['firstnamelabel'] = xarVarPrepForDisplay(xarML('First name '));
    $data['lastnamelabel'] = xarVarPrepForDisplay(xarML('Last name '));
    $data['tussenvgsllabel'] = xarVarPrepForDisplay(xarML('Tussenvoegsel(s)'));
    $data['initialslabel'] = xarVarPrepForDisplay(xarML('Initials'));
    $data['sexlabel'] = xarVarPrepForDisplay(xarML('Sexe'));
    $data['titlelabel'] = xarVarPrepForDisplay(xarML('Title'));
    $data['streetlabel'] = xarVarPrepForDisplay(xarML('Street'));
    $data['ziplabel'] = xarVarPrepForDisplay(xarML('ZIP Code'));
    $data['cityidlabel'] = xarVarPrepForDisplay(xarML('Town or City'));
    $data['phonehomelabel'] = xarVarPrepForDisplay(xarML('Home phone number'));

    $data['phoneworklabel'] = xarVarPrepForDisplay(xarML('Work phone number'));
    $data['emaillabel'] = xarVarPrepForDisplay(xarML('Email address'));
    $data['privphonehomelabel'] = xarVarPrepForDisplay(xarML('Home phone number private?'));
    $data['privworklabel'] = xarVarPrepForDisplay(xarML('Work address private?'));
    $data['privemaillabel'] = xarVarPrepForDisplay(xarML('Email address private?'));
    $data['privbirthdatelabel'] = xarVarPrepForDisplay(xarML('Birtdate private?'));
    $data['privaddresslabel'] = xarVarPrepForDisplay(xarML('Address private?'));
    $data['privphoneworklabel'] = xarVarPrepForDisplay(xarML('Work phone number private?'));
    $data['contactnamelabel'] = xarVarPrepForDisplay(xarML('Name of contact person '));
    $data['contactphonelabel'] = xarVarPrepForDisplay(xarML('Phone number of contact person'));
    $data['contactstreetlabel'] = xarVarPrepForDisplay(xarML('Street of contact person'));
    $data['contactcityidlabel'] = xarVarPrepForDisplay(xarML('Town or city of contact person'));
    $data['contactrelationlabel'] = xarVarPrepForDisplay(xarML('Relation to contact person'));
    $data['contactmobilelabel'] = xarVarPrepForDisplay(xarML('Mobile phone number of contact'));
    $data['birthdatelabel'] = xarVarPrepForDisplay(xarML('Birthdate'));
    $data['birthplacelabel'] = xarVarPrepForDisplay(xarML('Place of birth'));
    $data['nrkdistrictlabel'] = xarVarPrepForDisplay(xarML('NRK district'));
    $data['nrknumberlabel'] = xarVarPrepForDisplay(xarML('NRK registration number'));
    $data['ehbonrlabel'] = xarVarPrepForDisplay(xarML('First aid certificate number'));
    $data['ehbopluslabel'] = xarVarPrepForDisplay(xarML('First aid including bandages?'));
    $data['ehbodatelabel'] = xarVarPrepForDisplay(xarML('Date of renewal for first aid'));
    $data['ehboplacelabel'] = xarVarPrepForDisplay(xarML('Organisation to renew first aid'));
    $data['dateintakelabel'] = xarVarPrepForDisplay(xarML('Date of intake talk'));
    $data['intakebylabel'] = xarVarPrepForDisplay(xarML('Intake taken by'));
    $data['dateemploylabel'] = xarVarPrepForDisplay(xarML('Datum in dienst'));
    $data['dateoutlabel'] = xarVarPrepForDisplay(xarML('Datum uit dienst'));
    $data['dateouttalklabel'] = xarVarPrepForDisplay(xarML('Date of talk about departure'));
    $data['outreasonlabel'] = xarVarPrepForDisplay(xarML('Reason for departure'));
    $data['outtalkwithlabel'] = xarVarPrepForDisplay(xarML('Talk taken with'));
    $data['dateshoeslabel'] = xarVarPrepForDisplay(xarML('Date of shoes release'));
    $data['sizeshoeslabel'] = xarVarPrepForDisplay(xarML('Size of shoes'));
    $data['banknrlabel'] = xarVarPrepForDisplay(xarML('Bank account number'));
    $data['bankplaceidlabel'] = xarVarPrepForDisplay(xarML('Bank account place'));
    $data['otherslabel'] = xarVarPrepForDisplay(xarML('Other remarks'));
    $data['educationremarkslabel'] = xarVarPrepForDisplay(xarML('Education remarks'));



    // Get menu variables - it helps if all of the module pages have a standard
    // menu at their head to aid in navigation
    // $menu = xarModAPIFunc('sigmapersonnel','admin','menu','modify');
    $hooks = '';
    $item['module'] = 'sigmapersonnel';
    $hooks = xarModCallHooks('item', 'modify', $personid, $item);
    // Return the template variables defined in this function
    return array('authid'       => xarSecGenAuthKey(),
                 'invalid'      => $invalid,
                 'updatebutton' => xarVarPrepForDisplay(xarML('Update Person')),
                 'hookoutput'   => $hooks,
                 'item'         => $item);
}

?>
