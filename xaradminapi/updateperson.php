<?php
/**
 * Update a sigmapersonnel person
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
 * @author the MichelV <michelv@xaraya.com>
 * @param  $args ['personid'] the ID of the item
 * @param  $args ['firstname'] the first name of the person
 * @param  $args ['pnumber'] the new person number of the item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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

    if (!isset($firstname) || !is_string($firstname)) {
        $invalid[] = 'firstname';
    }
    if (!isset($lastname) || !is_string($lastname)) {
        $invalid[] = 'lastname';
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
    if (!xarSecurityCheck('EditSIGMAPersonnel', 1, 'PersonnelItem', "$personid:All:All")) {
        return;
    }

    // Implement situation of multiple parts of update

    if (!isset($userid)) {
        $userid = $item['userid'];
    }
    if (!isset($pnumber)) {
        $pnumber = $item['pnumber'];
    }
    if (!isset($persstatus)) {
        $persstatus = $item['persstatus'];
    }
    if (!isset($firstname)) {
        $firstname = $item['firstname'];
    }
    if (!isset($lastname)) {
        $lastname = $item['lastname'];
    }
    if (!isset($tussenvgsl)) {
        $tussenvgsl = $item['tussenvgsl'];
    }
    if (!isset($initials)) {
        $initials = $item['initials'];
    }
    if (!isset($sex)) {
        $sex = $item['sex'];
    }
    if (!isset($title)) {
        $title = $item['title'];
    }
    if (!isset($street)) {
        $street = $item['street'];
    }
    if (!isset($zip)) {
        $zip = $item['zip'];
    }
    if (!isset($cityid)) {
        $cityid = $item['cityid'];
    }
    if (!isset($phonehome)) {
        $phonehome = $item['phonehome'];
    }
    if (!isset($mobile)) {
        $mobile = $item['mobile'];
    }
    if (!isset($phonework)) {
        $phonework = $item['phonework'];
    }
    if (!isset($email)) {
        $email = $item['email'];
    }
    if (!isset($privphonehome)) {
        $privphonehome = $item['privphonehome'];
    }
    if (!isset($privwork)) {
        $privwork = $item['privwork'];
    }
    if (!isset($privemail)) {
        $privemail = $item['privemail'];
    }
    if (!isset($privbirthdate)) {
        $privbirthdate = $item['privbirthdate'];
    }
    if (!isset($privaddress)) {
        $privaddress = $item['privaddress'];
    }
    if (!isset($privphonework)) {
        $privphonework = $item['privphonework'];
    }
    if (!isset($contactname)) {
        $contactname = $item['contactname'];
    }
    if (!isset($contactphone)) {
        $contactphone = $item['contactphone'];
    }
    if (!isset($contactstreet)) {
        $contactstreet = $item['contactstreet'];
    }
    if (!isset($contactcityid)) {
        $contactcityid = $item['contactcityid'];
    }
    if (!isset($contactrelation)) {
        $contactrelation = $item['contactrelation'];
    }
    if (!isset($contactmobile)) {
        $contactmobile = $item['contactmobile'];
    }
    if (!isset($birthdate)) {
        $birthdate = $item['birthdate'];
    }
    if (!isset($birthplace)) {
        $birthplace = $item['birthplace'];
    }
    if (!isset($nrkdistrict)) {
        $nrkdistrict = $item['nrkdistrict'];
    }
    if (!isset($nrknumber)) {
        $nrknumber = $item['nrknumber'];
    }
    if (!isset($ehbonr)) {
        $ehbonr = $item['ehbonr'];
    }
    if (!isset($ehboplus)) {
        $ehboplus = $item['ehboplus'];
    }
    if (!isset($ehboplace)) {
        $ehboplace = $item['ehboplace'];
    }
    if (!isset($ehbodate)) {
        $ehbodate = $item['ehbodate'];
    }
    if (!isset($dateintake)) {
        $dateintake = $item['dateintake'];
    }
    if (!isset($intakeby)) {
        $intakeby = $item['intakeby'];
    }
    if (!isset($dateemploy)) {
        $dateemploy = $item['dateemploy'];
    }
    if (!isset($dateout)) {
        $dateout = $item['dateout'];
    }
    if (!isset($dateouttalk)) {
        $dateouttalk = $item['dateouttalk'];
    }
    if (!isset($outreason)) {
        $outreason = $item['outreason'];
    }
    if (!isset($outtalkwith)) {
        $outtalkwith = $item['outtalkwith'];
    }
    if (!isset($dateshoes)) {
        $dateshoes = $item['dateshoes'];
    }
    if (!isset($sizeshoes)) {
        $sizeshoes = $item['sizeshoes'];
    }
    if (!isset($banknr)) {
        $banknr = $item['banknr'];
    }
    if (!isset($bankplaceid)) {
        $bankplaceid = $item['bankplaceid'];
    }
    if (!isset($others)) {
        $others = $item['others'];
    }
    if (!isset($educationremarks)) {
        $educationremarks = $item['educationremarks'];
    }
    // dateformatting
/*
    if (!empty($birthdate) && is_string($birthdate)) {
        // Work around for failing calendar property
        list($year1, $month, $day) = split('-', $birthdate);
        $year = $year1+100;
        $birthdate = mktime(1576800000,0,0,$month,$day,$year);
*/

    if (!empty($birthdate) && !is_numeric($birthdate)) {
      $birthdate = safestrtotime($birthdate);
      //  $birthdate = $bdate+1576800000;
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

    // Last modification data
    $lastmodified = time();
    $lastmodifiedby = xarUserGetVar('uid');

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
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
function safestrtotime($strInput)
{
   $iVal = -1;
   for ($i=1900; $i<=1969; $i++) {
       // Check for this year string in date
       $strYear = (string)$i;
       if (!(strpos($strInput, $strYear)===false)) {
           $replYear = $strYear;
           $yearSkew = 1970 - $i;
           $strInput = str_replace($strYear, "1970", $strInput);
       };
   };
   $iVal = strtotime($strInput);
   if ($yearSkew > 0) {
       $numSecs = (60 * 60 * 24 * 365 * $yearSkew);
       $iVal = $iVal - $numSecs;
       $numLeapYears = 0;
       // Work out number of leap years in period
       for ($j=$replYear; $j<=1969; $j++) {
           $thisYear = $j;
           $isLeapYear = false;
           // Is div by 4?
           if (($thisYear % 4) == 0) {
               $isLeapYear = true;
           };
           // Is div by 100?
           if (($thisYear % 100) == 0) {
               $isLeapYear = false;
           };
           // Is div by 1000?
           if (($thisYear % 1000) == 0) {
               $isLeapYear = true;
           };
           if ($isLeapYear == true) {
               $numLeapYears++;
           };
       };
       $iVal = $iVal - (60 * 60 * 24 * $numLeapYears);
   };
   return($iVal);
};
?>
