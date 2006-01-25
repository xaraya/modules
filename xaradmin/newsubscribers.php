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

    // get other vars
    $authid = xarSecGenAuthKey();

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall');
    if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set template vars
    $data['authid'] = $authid;
    $data['pubs']   = $pubs;
    $data['pid']    = $pid;
    $data['stype']  = $stype;

    // handle items specific to subscriber type
    switch($stype) {
    case 'non':
        // nothing extra for non-registered users at present!
        break;
    case 'reg':
    default:

        if (!xarVarFetch('startnum', 'str:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

        // get other vars
        $subsperpage = xarModGetVar('ebulletin', 'admin_subsperpage');

        // get users
        $users = xarModAPIFunc('roles', 'user', 'getall', array(
            'include_myself' => false,
            'include_anonymous' => false,
            'startnum' => $startnum,
            'numitems' => $subsperpage
        ));
        if (empty($users) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // get pager
        $pager = xarTplGetPager(
            $startnum,
            xarModAPIFunc('roles', 'user', 'countall'),
            xarServerGetCurrentURL(array('startnum' => '%%')),
            $subsperpage
        );
        if (empty($pager) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // set additional template vars
        $data['pager']       = $pager;
        $data['users']       = $users;
        $data['subsperpage'] = $subsperpage;

    }

    return $data;
}

?>
