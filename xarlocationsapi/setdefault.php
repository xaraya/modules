<?php

function dossier_locationsapi_setdefault($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'Contact ID';
    }
    if (!isset($locationid) || !is_numeric($locationid)) {
        $invalid[] = 'Location ID';
    }
    
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'DOSSIER');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $contactinfo = xarModAPIFunc('dossier',
                            'user',
                            'get',
                            array('contactid' => $contactid));

    if (!isset($contactinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $locationinfo = xarModAPIFunc('dossier',
                            'locations',
                            'get',
                            array('locationid' => $locationid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];

    $query = "UPDATE $contactstable
            SET billinglocid = ?
            WHERE contactid = ?";

    $bindvars = array(
              $locationid,
              $contactid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    return true;
}
?>
