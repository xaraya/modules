<?php
/**
* Display GUI for adding new unregistered subscribers
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
* Display GUI for adding new unregistered subscribers
*
* pid
* unregistered - string of names and emails, one per line
*/
function ebulletin_admin_newsubscribers_unreg($args)
{
    // security check
    if (!xarSecurityCheck('AddeBulletin')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('pid', 'id', $pid, $pid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unregistered', 'str:1:', $unregistered, $unregistered, XARVAR_NOT_REQUIRED)) return;

    // get defaults
    if (empty($pid)) $pid = '';
    if (empty($unregistered)) $unregistered = '';

    // get other vars
    $authid = xarSecGenAuthKey();

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall');
    if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set template vars
    $data['unregistered'] = $unregistered;
    $data['authid']       = $authid;
    $data['pubs']         = $pubs;
    $data['pid']          = $pid;

    return $data;
}

?>
