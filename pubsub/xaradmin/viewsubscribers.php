<?php
/**
 * File: $Id$
 *
 * Pubsub admin viewSubscribers
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * Displays a list of subscribers to a given category. Provides an option
 * to manually remove a subscriber.
 */
function pubsub_admin_viewsubscribers()
{
    if (!xarVarFetch('catname', 'str::', $catname)) return;
    if (!xarVarFetch('cid',     'int::', $cid)) return;
    if (!xarVarFetch('pubsubid','int::', $pubsubid, FALSE)) return;
    if (!xarVarFetch('unsub',   'int::', $unsub, FALSE)) return;

	if ($unsub) {
        if (!xarModAPIFunc('pubsub',
	                       'user',
	                       'deluser',
	                        array('pubsubid' => $pubsubid))) {
	        $msg = xarML('Bad return from #(1) in function #(2)() in module #(3)',
	                     'deluser', 'viewsubscribers', 'Pubsub');
	        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
	                       new SystemException($msg));
	    }
	} 

    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Publish / Subscribe Administration'));
    $data['catname'] = xarVarPrepForDisplay($catname);
    $data['cid'] = $cid;
    $data['headinglabel'] = xarVarPrepForDisplay(xarML('Subscriber Summary'));
    $data['usernamelabel'] = xarVarPrepForDisplay(xarML('User Name'));
    $data['subdatelabel'] = xarVarPrepForDisplay(xarML('Date Subscribed'));
    $data['modnamelabel'] = xarVarPrepForDisplay(xarML('Module'));
    $data['actionlabel'] = xarVarPrepForDisplay(xarML('Action'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    if (!xarSecurityCheck('AdminPubSub')) return;

    // The user API function is called
    $subscribers = xarModAPIFunc('pubsub'
                                ,'admin'
                                ,'getsubscribers'
                                ,array('cid'=>$cid));

    $data['items'] = $subscribers;

	$data['returnurl'] = xarModURL('pubsub'
							      ,'admin'
							      ,'viewsubscribers'
							      ,array('catname'=>$catname
							            ,'cid'=>$cid));

	$data['removeParam'] = array('catname'=>$catname
	                            ,'cid'=>$cid
	                            ,'pubsubid'=>$pubsubid
	                            ,'unsub'=>1);
	                            					   
    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';

    // return the template variables defined in this template

    return $data;

} // END ViewSubscribers

?>
