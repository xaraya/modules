<?php
/**
 * File: $Id$
 *
 * Pubsub admin viewall
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
 * Displays a summary of category subscribtions and basic metrics. Provides options 
 * to view details about each subscription
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

?>
