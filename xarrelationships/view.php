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
function dossier_relationships_view($args)
{
    extract($args);
    
    if (!xarVarFetch('ownerid',   'int', $ownerid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid',   'int', $projectid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid',   'int', $clientid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdate',   'str::', $maxdate,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ttldays',   'int::', $ttldays,   7, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('dossier','admin','menu');

    $data['workrelationship'] = array();

    if (!xarSecurityCheck('ReadDossierLog')) {
        return;
    }

    $workrelationship = xarModAPIFunc('dossier',
                          'relationships',
                          'getall',
                          array('ownerid' => $ownerid,
                                'projectid' => $projectid,
                                'clientid' => $clientid,
                                'maxdate' => $maxdate,
                                'ttldays' => $ttldays));

    if (!isset($workrelationship) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['workrelationship'] = $workrelationship;
    $data['pager'] = '';
    return $data;
}

?>
