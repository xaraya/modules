<?php
/**
 * Get all persons in the database
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author Michel V.
 */
/**
 * Get all personnel
 *
 * @author the Michel V.
 * @param int numitems $ the number of items to retrieve (default -1 = all)
 * @param int startnum $ start with this item number (default 1)
 * @param str $sortby enum('firstname','lastname','pnumber','persstatus')
 * @param int catid
 * @returns array
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo Add check on status, either via privileges, or via here
 */
function sigmapersonnel_userapi_getall($args)
{
    extract($args);
    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    // Argument check
    $valid = array('firstname','lastname','pnumber','persstatus');
    if (!isset($sortby) || !in_array($sortby,$valid)) {
        $sortby = 'lastname';
    }
    // Argument check

    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'SIGMAPersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $items = array();
    // Security check
    if (!xarSecurityCheck('ViewSIGMAPersonnel')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sigmapersonneltable = $xartable['sigmapersonnel_person'];

    // Get items
    $query = "SELECT xar_personid,
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
                    xar_lastmodifiedby";

    // My try at categories
    if (!empty($catid) && xarModIsHooked('categories','sigmapersonnel')) {
        // Get the LEFT JOIN ... ON ...  and WHERE parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                       array('modid' => xarModGetIDFromName('sigmapersonnel'),
                                             'catid' => $catid));
        if (!empty($categoriesdef)) {
            $query .= " FROM ($sigmapersonneltable
                        LEFT JOIN $categoriesdef[table]
                        ON $categoriesdef[field] = xar_personid )
                        $categoriesdef[more]
                        WHERE $categoriesdef[where]";
            } else {
                $query .= " FROM $sigmapersonneltable";
            }
     } else {
        $query .= " FROM $sigmapersonneltable";
     }

    $query .= " ORDER BY $sigmapersonneltable.xar_" . $sortby;

    // SelectLimit also supports bind variable
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
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
        if (xarSecurityCheck('ViewSIGMAPersonnel', 0, 'PersonnelItem', "$personid:All:$persstatus")) {
            $items[] = array(
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
                            'lastmodified' => $lastmodified,
                            'lastmodifiedby' => $lastmodifiedby);
        }
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Return the items
    return $items;
}

?>
