<?php
/**
 * File: $Id$
 *
 * Admin interface for the pubsub module
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
 * the main administration function
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminPubSub')) return;

    // Return the template variables defined in this function
    return array();
}

/**
 * Admin Menu
*/
function pubsub_admin_viewall()
{

    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Publish / Subscribe Administration'));
    $data['headinglabel'] = xarVarPrepForDisplay(xarML('Subscription Summary'));
    $data['modnamelabel'] = xarVarPrepForDisplay(xarML('Module Name'));
    $data['categorylabel'] = xarVarPrepForDisplay(xarML('Category'));
    $data['itemlabel'] = xarVarPrepForDisplay(xarML('Item'));
    $data['numsubscriberslabel'] = xarVarPrepForDisplay(xarML('Subscribers'));
    $data['actionlabel'] = xarVarPrepForDisplay(xarML('Action'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

	if (!xarSecurityCheck('AdminPubSub')) return;

    // The user API function is called
    $events = xarModAPIFunc('pubsub',
                            'admin',
                            'getall');

    $data['items'] = $events;

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';

    // return the template variables defined in this template

    return $data;

} // END ViewAll

/**
 * ViewSubscribers
 */
function pubsub_admin_viewsubscribers()
{
    xarVarFetch('catname', 'str::', $catname);
    xarVarFetch('cid',     'int::', $cid);

    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Publish / Subscribe Administration'));
    $data['catname'] = xarVarPrepForDisplay($catname);
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

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';

    // return the template variables defined in this template

    return $data;

} // END ViewSubscribers

?>
