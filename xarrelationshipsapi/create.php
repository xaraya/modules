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
function dossier_relationshipsapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (!isset($connectedid) || !is_numeric($connectedid)) {
        $invalid[] = 'connectedid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'relationships', 'create', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    $item = xarModAPIFunc('dossier',
                            'user',
                            'get',
                            array('contactid' => $contactid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('AddDossierLog', 1, 'Log', $item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid'])) {
        $msg = xarML('Not authorized to add #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $relationshipstable = $xartable['dossier_relationships'];

    $nextId = $dbconn->GenId($relationshipstable);
    
    $dateadded = date("Y-m-d H:i:s");

    $query = "INSERT INTO $relationshipstable (
                  relationshipid,
                  contactid,
                  connectedid,
                  relationship,
                  dateadded,
                  private,
                  notes)
            VALUES (?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $contactid,
              $connectedid,
              $relationship,
              $dateadded,
              $private,
              $notes);
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $relationshipid = $dbconn->PO_Insert_ID($relationshipstable, 'relationshipid');

    return $relationshipid;
}

?>
