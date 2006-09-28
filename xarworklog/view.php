<?php
/**
 * XTask Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XTask Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_worklog_view($args)
{
    extract($args);
    
    if (!xarVarFetch('ownerid',   'int', $ownerid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid',   'int', $projectid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid',   'int', $clientid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdate',   'str::', $maxdate,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ttldays',   'int::', $ttldays,   7, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('xtasks','admin','menu');

    $data['worklog'] = array();

    if (!xarSecurityCheck('ViewXTask')) {
        return;
    }

    $worklog = xarModAPIFunc('xtasks',
                          'worklog',
                          'getall',
                          array('ownerid' => $ownerid,
                                'projectid' => $projectid,
                                'clientid' => $clientid,
                                'maxdate' => $maxdate,
                                'ttldays' => $ttldays));

    if (!isset($worklog) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['worklog'] = $worklog;
    $data['pager'] = '';
    return $data;
}

?>
