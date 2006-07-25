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
function xtasks_user_view($args)
{
    extract($args);
    if (!xarVarFetch('startnum',   'int:1:', $startnum,   1, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('xtasks','user','menu');

    $data['items'] = array();

    if (!xarSecurityCheck('ViewXTask')) {
        return;
    }

    $xtaskss = xarModAPIFunc('xtasks',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => 10));//TODO: numitems

    if (!isset($xtaskss) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    for ($i = 0; $i < count($xtaskss); $i++) {
        $project = $xtaskss[$i];
//		if (xarSecAuthAction(0, 'xtasks::Tasks', "$project[name]::$project[projectid]", ACCESS_READ)) {
        if (xarSecurityCheck('ReadXTask', 0, 'Item', "$project[name]:All:$project[projectid]")) {//TODO: security
            $xtaskss[$i]['link'] = xarModURL('xtasks',
                                               'user',
                                               'display',
                                               array('projectid' => $project['projectid']));
        }
        //if (xarSecAuthAction(0, 'xtasks::Tasks', "$project[name]::$project[projectid]", ACCESS_EDIT)) {
        if (xarSecurityCheck('EditXTask', 0, 'Item', "$project[name]:All:$project[projectid]")) {//TODO: security
            $xtaskss[$i]['editurl'] = xarModURL('xtasks',
                                               'admin',
                                               'modify',
                                               array('projectid' => $project['projectid']));
        } else {
            $xtaskss[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXTask', 0, 'Item', "$project[name]:All:$project[projectid]")) {
            $xtaskss[$i]['deleteurl'] = xarModURL('xtasks',
                                               'admin',
                                               'delete',
                                               array('projectid' => $project['projectid']));
        } else {
            $xtaskss[$i]['deleteurl'] = '';
        }
    }

    $data['xtaskss'] = $xtaskss;
    $data['pager'] = '';
    return $data;
}

?>
