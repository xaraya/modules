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
*/

/**
 * the main administration function
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecurityCheck('EditPubSub')) return;

    // Return the template variables defined in this function
    return array();
}

/**
 * Admin Menu
*/
function pubsub_admin_view()
{
    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarMLByKey('PUBSUBNAME'));
    $data['modnamelabel'] = xarVarPrepForDisplay(xarML('Module Name'));
    $data['categorylabel'] = xarVarPrepForDisplay(xarML('Category'));
    $data['itemlabel'] = xarVarPrepForDisplay(xarML('Item'));
    $data['numsubscriberslabel'] = xarVarPrepForDisplay(xarML('Number of Subscribers'));
    $data['templatelabel'] = xarVarPrepForDisplay(xarML('Template'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    if (!xarSecurityCheck('EditPubSub')) return;

    // The user API function is called
    $events = xarModAPIFunc('pubsub',
                            'user',
                            'getall');

    $data['items'] = $events;

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';
    
    // return the template variables defined in this template
    return $data;   
}
?>
