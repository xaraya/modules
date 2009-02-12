<?php

function dossier_relationshipsapi_get($args)
{
    extract($args);

    if (!isset($relationshipid) || !is_numeric($relationshipid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'relationshipid', 'relationships', 'get', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $relationshipstable = $xartable['dossier_relationships'];

    $query = "SELECT relationshipid,
                  contactid,
                  connectedid,
                  relationship,
                  dateadded,
                  private,
                  notes
            FROM $relationshipstable
            WHERE relationshipid = ?";
    $result = &$dbconn->Execute($query,array($relationshipid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($relationshipid,
          $contactid,
          $connectedid,
          $relationship,
          $dateadded,
          $private,
          $notes) = $result->fields;

    $result->Close();

    if (!xarSecurityCheck('ReadDossierLog', 1, 'Log', "All:All:All:All")) {
        $msg = xarML('Not authorized to view reminders.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('relationshipid'       => $relationshipid,
                  'contactid'   => $contactid,
                  'connectedid'     => $connectedid,
                  'relationship'     => $relationship,
                  'dateadded'     => $dateadded,
                  'private'  => $private,
                  'notes'       => $notes);

    return $item;
}

?>
