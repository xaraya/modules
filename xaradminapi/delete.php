<?php
/**
 * Dossier Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
function dossier_adminapi_delete($args)
{
    extract($args);

    if (!isset($contactid) || !is_numeric($contactid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Contact ID', 'admin', 'delete', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    
    $uid = xarSessionGetVar('uid');

    // does it exist ?
    $item = xarModAPIFunc('dossier',
                            'user',
                            'get',
                            array('contactid' => $contactid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if ($item['userid'] != $uid) {
        if (!xarSecurityCheck('AuditDossierLog', 1, 'Contact', $item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid'])) {
            $msg = xarML('Not authorized to delete #(1) item #(2)',
                        'dossier', xarVarPrepForStore($projectid));
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                           new SystemException($msg));
            return;
        }
    }
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];

    // does it have children ?
    $sql = "DELETE FROM $contactstable
            WHERE contactid = " . $contactid;
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR: '. $sql);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // COMMENTED OUT BECAUSE IT WILL TRY TO DELETE ROLES ENTRIES IF HOOKED
    // TODO: HOW DO WE PREVENT THIS?
//    $item['module'] = 'dossier';
//    $item['itemid'] = $contactid;
//    xarModCallHooks('item', 'delete', $contactid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
