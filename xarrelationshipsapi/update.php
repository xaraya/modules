<?php

function dossier_relationshipsapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($relationshipid) || !is_numeric($relationshipid)) {
        $invalid[] = 'relationshipid';
    }
    if (!isset($connectedid) || !is_numeric($connectedid)) {
        $invalid[] = 'connectedid';
    }
    if (!isset($relationship) || !is_string($relationship)) {
        $invalid[] = 'relationshipdate';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'relationships', 'update', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('dossier',
                            'relationships',
                            'get',
                            array('relationshipid' => $relationshipid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('MyDossierLog', 1, 'Log')) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $relationshipstable = $xartable['dossier_relationships'];

    $query = "UPDATE $relationshipstable
              SET connectedid =?, 
                  relationship = ?,
                  private = ?,
                  notes = ?
              WHERE relationshipid = ?";

    $bindvars = array(
              $connectedid,
              $relationship,
              $private,
              $notes,
              $relationshipid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    return true;
}
?>
