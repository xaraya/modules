<?php
/**
 * Dossier Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author Chad Kraeft
 */
function dossier_locationsapi_deletedata($args)
{
    extract($args);

    $invalid = array();
    if (!isset($locationid) || !is_numeric($locationid)) {
        $invalid[] = 'location ID';
    }
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contact ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'locations', 'deletedata', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!isset($locationid) || !is_numeric($locationid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'location ID', 'locations', 'delete', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('dossier',
                        'locations',
                        'getcontact',
                        array('locationid' => $locationid,
                            'contactid' => $contactid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $contactinfo = xarModAPIFunc('dossier',
                        'user',
                        'get',
                        array('contactid' => $contactid));

    if (!isset($contactinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', $contactinfo['cat_id'].":".$contactinfo['userid'].":".$contactinfo['company'].":".$contactinfo['agentuid'])) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'dossier', $projectid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $locationdatatable = $xartable['dossier_locationdata'];

    // does it have children ?
    $sql = "DELETE FROM $locationdatatable
            WHERE locationid = " . $locationid . "
            AND contactid = " . $contactid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    return true;
}

?>
