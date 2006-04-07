<?php
/**
* Display GUI for adding registered subscribers
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Display GUI for adding registered subscribers
*
* pid
* registered - array of ids
*/
function ebulletin_admin_newsubscribers($args)
{
    // security check
    if (!xarSecurityCheck('AddeBulletin')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('pid', 'id', $pid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('stype', 'enum:reg:non', $stype, 'reg', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'str:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'str:1:', $numitems, '', XARVAR_NOT_REQUIRED)) return;

    // get other vars
    if (empty($numitems) || !is_numeric($numitems)) {
        $numitems = xarSessionGetVar('ebulletin_subsperpage');
        if (empty($numitems)) {
            $numitems = xarModGetVar('ebulletin', 'admin_subsperpage');
        }
    } else {
        xarSessionSetVar('ebulletin_subsperpage', $numitems);
    }


    // get other vars
    $authid = xarSecGenAuthKey();

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall');
    if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set template vars
    $data = array();
    $data['authid']   = $authid;
    $data['pubs']     = $pubs;
    $data['pid']      = $pid;
    $data['stype']    = $stype;
    $data['numitems'] = $numitems;

    // handle items specific to subscriber type
    switch($stype) {
    case 'non':
        // nothing extra for non-registered users at present!
        break;
    case 'reg':
    default:

        // get users
        $users = xarModAPIFunc('roles', 'user', 'getall', array(
            'include_myself' => false,
            'include_anonymous' => false,
            'state' => 3,
            'startnum' => $startnum,
            'numitems' => $numitems
        ));
        if (empty($users) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // get pager
        $pager = xarTplGetPager(
            $startnum,
            xarModAPIFunc('roles', 'user', 'countall'),
            xarServerGetCurrentURL(array('startnum' => '%%')),
            $numitems
        );
        if (empty($pager) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // set additional template vars
        $data['pager']       = $pager;
        $data['users']       = $users;

    }

    return $data;
}

?>
