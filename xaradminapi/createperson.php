<?php
/**
 * Create a new sigma person
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
 * create a new sigmapersonnel item
 *
 * @author the Michel V.
 * @param  $args ['firstname'] firstname of the item
 * @param  $args ['pnumber'] number of the item
 * @returns int
 * @return sigmapersonnel item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function sigmapersonnel_adminapi_createperson($args)
{
    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($firstname) || !is_string($firstname)) {
        $invalid[] = 'firstname';
    }
    if (!isset($lastname) || !is_string($lastname)) {
        $invalid[] = 'lastname';
    }
    /* Need to think this over.
    if (!isset($pnumber) || !is_numeric($pnumber)) {
        $invalid[] = 'pnumber';
    }
    */
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'SIGMAPersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check
    if (!xarSecurityCheck('AddSIGMAPersonnel', 1, 'PersonnelItem', "All:All:$persstatus")) {
        return;
    }
    $lastmodified = time();
    $lastmodifiedby = xarUserGetVar('uid');

    if (!empty($birthdate) && !is_numeric($birthdate)) {
        // Work around for failing calendar property
        /*list($year1, $month, $day) = split('-', $birthdate);
        $year = $year1 +50;
        $birthdate = mktime(0,0,0,$month,$day,$year);*/
        $bdate = strtotime($birthdate);
        $birthdate = $bdate+3153600000;
    }

    if (!empty($ehbodate) && !is_numeric($ehbodate)) {
        $ehbodate = strtotime($ehbodate);
    }
    if (!empty($dateintake) && !is_numeric($dateintake)) {
        $dateintake = strtotime($dateintake);
    }
    if (!empty($dateout) && !is_numeric($dateout)) {
        $dateout = strtotime($dateout);
    }
    if (!empty($dateouttalk) && !is_numeric($dateouttalk)) {
        $dateouttalk = strtotime($dateouttalk);
    }
    if (!empty($dateemploy) && !is_numeric($dateemploy)) {
        $dateemploy = strtotime($dateemploy);
    }
    if (!empty($dateshoes) && !is_numeric($dateshoes)) {
        $dateshoes = strtotime($dateshoes);
    }


    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    // Get next ID in table
    $nextId = $dbconn->GenId($sigmapersonneltable);
    // Add item
    $query = "INSERT INTO $sigmapersonneltable (
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
        xar_lastmodifiedby)
            VALUES (?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,
            ?,?)";
    $bindvars = array($nextId,
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
        ($privphonehome ? 1 : 0),
        ($privwork ? 1 : 0),
        ($privemail ? 1 : 0),
        ($privbirthdate ? 1 : 0),
        ($privaddress ? 1 : 0),
        ($privphonework ? 1 : 0),
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
        ($ehboplus ? 1 : 0),
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
        $lastmodifiedby);
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Get the ID of the item that we inserted.
    $personid = $dbconn->PO_Insert_ID($sigmapersonneltable, 'xar_personid');
    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'sigmapersonnel';
    $item['itemid'] = $personid;
    xarModCallHooks('item', 'create', $personid, $item);
    // Return the id of the newly created item to the calling process
    return $personid;
}

?>
