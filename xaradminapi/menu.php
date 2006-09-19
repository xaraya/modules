<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * Generate admin menu
 *
 * Standard function to generate a common admin menu configuration for the module
 *
 * @author St.Ego
 */
function xproject_adminapi_menu($args)
{
    extract($args);

    if(!xarModLoad('addressbook', 'user')) return;

    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');

    if (!xarVarFetch('verbose', 'checkbox', $verbose, $verbose, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('q', 'str', $q, '', XARVAR_GET_OR_POST)) return;
    if($q) xarSessionSetVar('q', $q);
    else xarSessionDelVar('q');
    if (!xarVarFetch('status', 'str', $status, '', XARVAR_NOT_REQUIRED)) return;
    if($status) xarSessionSetVar('status', $status);
    else xarSessionDelVar('status');
    if (!xarVarFetch('projecttype', 'str', $projecttype, '', XARVAR_NOT_REQUIRED)) return;
    if($projecttype) xarSessionSetVar('projecttype', $projecttype);
    else xarSessionDelVar('projecttype');
    if (!xarVarFetch('max_priority', 'int', $max_priority, 9, XARVAR_NOT_REQUIRED)) return;
    if($max_priority) xarSessionSetVar('max_priority', $max_priority);
    if (!xarVarFetch('max_importance', 'int', $max_importance, 9, XARVAR_NOT_REQUIRED)) return;
    if($max_importance) xarSessionSetVar('max_importance', $max_importance);
    if (!xarVarFetch('showsearch', 'str', $showsearch, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'int', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if($memberid) xarSessionSetVar('memberid', $memberid);
    else xarSessionDelVar('memberid');
    if (!xarVarFetch('inline',     'int',     $inline,     0,     XARVAR_NOT_REQUIRED)) return;

    $teammembers = xarModAPIFunc('xproject', 'team', 'getmembers');

    $menu = array();
    $menu['menutitle'] = xarML('xProject Administration');
    $menu['memberid'] = $memberid;
    $menu['inline'] = $inline;
    $menu['verbose'] = $verbose;
    $uid = xarSessionGetVar('uid');
    $mymemberid = xarModGetUserVar('xproject', 'mymemberid');
    $menu['mymemberid'] = $mymemberid ? $mymemberid : "0";
    $menu['status'] = $status;
    $menu['draftstatus'] = $draftstatus;
    $menu['activestatus'] = $activestatus;
    $menu['archivestatus'] = $archivestatus;
    $menu['projecttype'] = $projecttype;
    $menu['statusmsg'] = xarSessionGetVar('statusmsg');
    xarSessionDelVar('statusmsg');
    $menu['q'] = $q;
    $menu['sortby'] = $sortby;
    $menu['max_priority'] = $max_priority;
    $menu['max_importance'] = $max_importance;
    $menu['showsearch'] = $showsearch;
    $menu['teammembers'] = $teammembers;

    return $menu;
}
?>