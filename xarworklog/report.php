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
function xtasks_worklog_report($args)
{
    extract($args);
    
    if (!xarVarFetch('ownerid',   'int', $ownerid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownersearch',   'checkbox', $ownersearch,   "", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid',   'int', $projectid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid',   'int', $clientid,   0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mindate',   'str::', $mindate,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxdate',   'str::', $maxdate,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reportview',   'str::', $reportview,   "", XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ttldays',   'int::', $ttldays,   7, XARVAR_NOT_REQUIRED)) return;
    
    if(!$ownersearch) $ownerid = NULL;

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
                                'mindate' => $mindate,
                                'maxdate' => $maxdate,
                                'ttldays' => $ttldays));

    if (!isset($worklog) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $worksummary = array();

    if(empty($reportview)) {
        $filter_1 = "thisprojectid";
        $filter_2 = "taskownerid";
        $filter_3 = "workdate";
    } else {
        list($filter_1,$filter_2,$filter_3) = split(":",$reportview);
	}

    foreach($worklog as $workinfo) {
        list($workdate,$worktime) = explode(" ", $workinfo['eventdate']);
        
        $taskinfo = xarModAPIFunc('xtasks','user','get',array('taskid' => $workinfo['taskid']));
        $thisprojectid = $taskinfo['projectid'] ? $taskinfo['projectid'] : 0;
        $taskownerid = $workinfo['ownerid'] ? $workinfo['ownerid'] : 0;
//        $worksummary[$thisprojectid][$taskownerid] = array();
        
        $hours = $workinfo['hours'] > 0 ? $workinfo['hours'] : "none";

        if(!isset($worksummary[$$filter_1][$$filter_2][$$filter_3])) {
            $worksummary[$$filter_1][$$filter_2][$$filter_3]['ttltasks'] = 1;
            $worksummary[$$filter_1][$$filter_2][$$filter_3]['hours'] = $workinfo['hours'];
//echo "<br>$thisprojectid - $taskownerid - $workdate - $hours - ".$worksummary[$thisprojectid][$taskownerid][$workdate];
        } elseif($worksummary[$$filter_1][$$filter_2][$$filter_3]['hours'] == "none") {
            $worksummary[$$filter_1][$$filter_2][$$filter_3]['ttltasks'] = 1;
            $worksummary[$$filter_1][$$filter_2][$$filter_3]['hours'] = $workinfo['hours'];
        } else {
            $worksummary[$$filter_1][$$filter_2][$$filter_3]['ttltasks']++;
            $worksummary[$$filter_1][$$filter_2][$$filter_3]['hours'] = $worksummary[$$filter_1][$$filter_2][$$filter_3]['hours'] + $workinfo['hours'];
        }
    }
//echo "<pre>"; print_r($worksummary); echo "</pre>";
    $data['worklog'] = $worklog;
    $data['worksummary'] = $worksummary;
    $data['mindate'] = $mindate;
    $data['maxdate'] = $maxdate;
    $data['reportview'] = $reportview;
    $data['ownerid'] = $ownerid;
    $data['ownersearch'] = $ownersearch;
    $data['projectid'] = $projectid;
    $data['ttldays'] = $ttldays;
    $data['pager'] = '';
    return $data;
}

?>
