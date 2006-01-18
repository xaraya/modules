<?php
/**
 * Update an sigmapersonnel item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author Michel V.
 */
/**
 * update an sigmapersonnel item
 *
 * @author the Michel V.
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function sigmapersonnel_adminapi_updateperson($args)
{
    extract($args);
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($personid) || !is_numeric($personid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($lastname) || !is_string($lastname)) {
        $invalid[] = 'lastname';
    }
    if (!isset($firstname) || !is_string($firstname)) {
        $invalid[] = 'firstname';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updateperson', 'SIGMAPersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get the original item
    $item = xarModAPIFunc('sigmapersonnel',
        'user',
        'get',
        array('personid' => $personid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditSIGMAPersonnel', 1, 'PersonnelItem', "$PersonID:All:All")) {
        return;
    }

    // Implement situation of multiple parts of update

    // Get database setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];
    // Update the item
    $query = "UPDATE $sigmapersonneltable
            SET
        xar_userid =?,
        xar_pnumber =?,
        xar_persstatus =?,
        xar_firstname =?,
        xar_lastname =?,
        xar_tussenvgsl =?,
        xar_initials =?,
        xar_sex =?,
        xar_title =?,
        xar_street =?,
        xar_zip =?,
        xar_cityid =?,
        xar_phonehome =?,
        xar_mobile =?,
        xar_phonework =?,
        xar_email =?,
        xar_privphonehome =?,
        xar_privwork =?,
        xar_privemail =?,
        xar_privbirthdate =?,
        xar_privaddress =?,
        xar_privphonework =?,
        xar_contactname =?,
        xar_contactphone =?,
        xar_contactstreet =?,
        xar_contactcityid =?,
        xar_contactrelation =?,
        xar_contactmobile =?,
        xar_birthdate =?,
        xar_birthplace =?,
        xar_nrkdistrict =?,
        xar_nrknumber =?,
        xar_ehbonr =?,
        xar_ehboplus =?,
        xar_ehbodate =?,
        xar_ehboplace =?,
        xar_dateintake =?,
        xar_intakeby =?,
        xar_dateemploy =?,
        xar_dateout =?,
        xar_dateouttalk =?,
        xar_outreason =?,
        xar_outtalkwith =?,
        xar_dateshoes =?,
        xar_sizeshoes =?,
        xar_banknr =?,
        xar_bankplaceid =?,
        xar_others =?,
        xar_educationremarks =?,
        xar_lastmodified =?,
        xar_lastmodifiedby =?

        WHERE xar_personid = ?";
    $bindvars = array($userid,
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
        $lastmodifiedby,
        $personid);
    $result = &$dbconn->Execute($query,$bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'sigmapersonnel';
    $item['itemid'] = $personid;
    $item['itemtype'] = 1;

    xarModCallHooks('item', 'update', $personid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
