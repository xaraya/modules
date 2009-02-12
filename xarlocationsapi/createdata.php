<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function dossier_locationsapi_createdata($args)
{
    extract($args);

    $invalid = array();
    if (!isset($locationid) || !is_numeric($locationid)) {
        $invalid[] = 'locationid';
    }
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'locations', 'createdata', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    if (!isset($startdate) || !is_string($startdate) || empty($startdate)) {
        $startdate = NULL;
    } else {
        $startdate = date("Y-m-d", strtotime($startdate));
    }
    if (!isset($enddate) || !is_string($enddate) || empty($enddate)) {
        $enddate = NULL;
    } else {
        $enddate = date("Y-m-d", strtotime($enddate));
    }
    
    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', "All:All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $locationdatatable = $xartable['dossier_locationdata'];

    $nextId = $dbconn->GenId($locationdatatable);

    $query = "INSERT INTO $locationdatatable (
                    locationid,
                    contactid,
                    startdate,
                    enddate)
                VALUES (?,?,?,?)";

    $bindvars = array($locationid,
                    $contactid,
                    $startdate,
                    $enddate);

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    return true;
}

?>
