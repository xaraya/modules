<?php
/**
 * XTask Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XTask Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function dossier_logs_staff($args)
{
    extract($args);
    
    if (!xarVarFetch('ownerid',   'int', $ownerid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid',   'int', $projectid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid',   'int', $clientid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdate',   'str::', $maxdate,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ttldays',   'int::', $ttldays,   7, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('dossier','admin','menu');

    $data['contactlogs'] = array();

    if (!xarSecurityCheck('AuditDossierLog')) {
        return;
    }

    $contactlogs = xarModAPIFunc('dossier',
                          'logs',
                          'getall',
                          array('ownerid' => $ownerid,
                                'projectid' => $projectid,
                                'clientid' => $clientid,
                                'maxdate' => $maxdate,
                                'ttldays' => $ttldays,
                                'numitems' => xarModGetVar('dossier','itemsperpage')));

    if (!isset($contactlogs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['ownerid'] = $ownerid;
    $data['contactlogs'] = $contactlogs;
    $data['pager'] = '';
    return $data;
}

?>
