<?php
/**
 * Get a specific person
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * get a specific person
 *
 * @author the Michel V.
 * @param  id $personid ID of sigmapersonnel item to get
 * @returns array
 * @return item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sigmapersonnel_userapi_get($args)
{
    extract($args);

    // Argument check
    if (!isset($personid) || !is_numeric($personid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'SIGMA Personnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get database setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    // Get item
    $query = "SELECT
            xar_personid,
            xar_userid,
            xar_pnumber,
            xar_persstatus,
            xar_firstname,
            xar_lastname,
            xar_tussenvgsl,
            xar_initials,
            xar_sex,
            xar_title,
            xar_street,
            xar_zip,
            xar_cityid,
            xar_phonehome,
            xar_mobile,
            xar_phonework,
            xar_email,
            xar_privphonehome,
            xar_privwork,
            xar_privemail,
            xar_privbirthdate,
            xar_privaddress,
            xar_privphonework,
            xar_contactname,
            xar_contactphone,
            xar_contactstreet,
            xar_contactcityid,
            xar_contactrelation,
            xar_contactmobile,
            xar_birthdate,
            xar_birthplace,
            xar_nrkdistrict,
            xar_nrknumber,
            xar_ehbonr,
            xar_ehboplus,
            xar_ehbodate,
            xar_ehboplace,
            xar_dateintake,
            xar_intakeby,
            xar_dateemploy,
            xar_dateout,
            xar_dateouttalk,
            xar_outreason,
            xar_outtalkwith,
            xar_dateshoes,
            xar_sizeshoes,
            xar_banknr,
            xar_bankplaceid,
            xar_others,
            xar_educationremarks,
            xar_lastmodified,
            xar_lastmodifiedby
            FROM $sigmapersonneltable
            WHERE xar_personid = ?";
    $result = &$dbconn->Execute($query,array($personid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // Obtain the item information from the result set
    list($personid,
        $userid,
        $pnumber,
        $persstatus,
        $firstname,
        $lastname,
        $tussenvgsl,
        $initials,
        $sex,
        $title,
        $street,
        $zip,
        $cityid,
        $phonehome,
        $mobile,
        $phonework,
        $email,
        $privphonehome,
        $privwork,
        $privemail,
        $privbirthdate,
        $privaddress,
        $privphonework,
        $contactname,
        $contactphone,
        $contactstreet,
        $contactcityid,
        $contactrelation,
        $contactmobile,
        $birthdate,
        $birthplace,
        $nrkdistrict,
        $nrknumber,
        $ehbonr,
        $ehboplus,
        $ehbodate,
        $ehboplace,
        $dateintake,
        $intakeby,
        $dateemploy,
        $dateout,
        $dateouttalk,
        $outreason,
        $outtalkwith,
        $dateshoes,
        $sizeshoes,
        $banknr,
        $bankplaceid,
        $others,
        $educationremarks,
        $lastmodified,
        $lastmodifiedby) = $result->fields;

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Security check
    if (!xarSecurityCheck('ReadSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:$persstatus")) {
        return;
    }

    // Format the birthdate
    // Workaround due to failing calendar property
   /* if (!empty($birthdate) && !is_numeric($birthdate)) {
        // Work around for failing calendar property
        list($year1, $month, $day) = split('-', $birthdate);
        $year = $year1 +50;
        $birthdate = mktime(0,0,0, $month,$day, $year);
    }
    $bdate = date('Y-m-d', $birthdate);
    list($year1, $month, $day) = split('-', $bdate);
    $year = $year1 -50;
    $birthdate = mktime(0,0,0,$month,$day,$year);*/
    /*$bdate = strtotime($birthdate);
    $offset = 1576800000;
        $birthdate = ($birthdate-$offset);
*/
    // Create the item array
    $item = array(
        'personid'      => $personid,
        'userid'        => $userid,
        'pnumber'       => $pnumber,
        'persstatus'    => $persstatus,
        'firstname'     => $firstname,
        'lastname'      => $lastname,
        'tussenvgsl'    => $tussenvgsl,
        'initials'      => $initials,
        'sex'           => $sex,
        'title'         => $title,
        'street'        => $street,
        'zip'           => $zip,
        'cityid'        => $cityid,
        'phonehome'     => $phonehome,
        'mobile'        => $mobile,
        'phonework'     => $phonework,
        'email'         => $email,
        'privphonehome' => $privphonehome,
        'privwork'      => $privwork,
        'privemail'     => $privemail,
        'privbirthdate' => $privbirthdate,
        'privaddress'   => $privaddress,
        'privphonework' => $privphonework,
        'contactname'   => $contactname,
        'contactphone'  => $contactphone,
        'contactstreet' => $contactstreet,
        'contactcityid' => $contactcityid,
        'contactrelation' => $contactrelation,
        'contactmobile' => $contactmobile,
        'birthdate'     => $birthdate,
        'birthplace'    => $birthplace,
        'nrkdistrict'   => $nrkdistrict,
        'nrknumber'     => $nrknumber,
        'ehbonr'        => $ehbonr,
        'ehboplus'      => $ehboplus,
        'ehbodate'      => $ehbodate,
        'ehboplace'     => $ehboplace,
        'dateintake'    => $dateintake,
        'intakeby'      => $intakeby,
        'dateemploy'    => $dateemploy,
        'dateout'       => $dateout,
        'dateouttalk'   => $dateouttalk,
        'outreason'     => $outreason,
        'outtalkwith'   => $outtalkwith,
        'dateshoes'     => $dateshoes,
        'sizeshoes'     => $sizeshoes,
        'banknr'        => $banknr,
        'bankplaceid'   => $bankplaceid,
        'others'        => $others,
        'educationremarks' => $educationremarks,
        'lastmodified'  => $lastmodified,
        'lastmodifiedby'=> $lastmodifiedby);
    // Return the item array
    return $item;
}

?>