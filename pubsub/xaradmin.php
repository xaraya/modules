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
    if (!xarSecAuthAction(0, 'Pubsub::', '::', ACCESS_EDIT)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $welcome = '';
    // TODO: make directory non-hardcoded
    @include('modules/pubsub/xarlang/eng/admindoc.php');
    
    // Return the template variables defined in this function
    return array('menu' => pubsub_admin_getmenu(),
                 'welcome' => $welcome);
}

/**
 * Admin Menu
*/
function pubsub_admin_view()
{
    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarMLByKey('PUBSUBNAME'));
    $data['modnamelabel'] = xarVarPrepForDisplay(xarMLByKey('Module Name'));
    $data['categorylabel'] = xarVarPrepForDisplay(xarMLByKey('Category'));
    $data['itemlabel'] = xarVarPrepForDisplay(xarMLByKey('Item'));
    $data['numsubscriberslabel'] = xarVarPrepForDisplay(xarMLByKey('Number of Subscribers'));
    $data['templatelabel'] = xarVarPrepForDisplay(xarMLByKey('Template'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarMLByKey('Options'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    if (!xarSecAuthAction(0, 'Pubsub::', '::', ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    // Load API
    if (!xarModAPILoad('pubsub', 'user')) return;
    // The user API function is called
    $events = xarModAPIFunc('pubsub',
                          'user',
                          'getall');

    if (empty($events)) return;
    
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($events); $i++) {
        $event = $events[$i];
        if (xarSecAuthAction(0, 'Pubsub::', "::", ACCESS_EDIT)) {
            $events[$i]['editurl'] = xarModURL('pubsub',
                                              'admin',
                                              'modify',
                                              array('hid' => $event['hid']));
        } else {
            $events[$i]['editurl'] = '';
        }
        $events[$i]['edittitle'] = xarML('Edit');
        if (xarSecAuthAction(0, 'Pubsub::', "::", ACCESS_DELETE)) {
            $events[$i]['deleteurl'] = xarModURL('pubsub',
                                                'admin',
                                                'delete',
                                                array('hid' => $event['hid']));
        } else {
            $events[$i]['deleteurl'] = '';
        }
        $events[$i]['deletetitle'] = xarML('Delete');
    }
    $data['items'] = $events;

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';
    
    // return the template variables defined in this template
    return $data;   
}
/**
 * Main administrative menu labels
*/
function pubsub_admin_getmenu()
{
    $menu = array();

    $menu['status'] = xarGetStatusMsg();
    $menu['title'] = xarML('Pubsub Administration');
    $menu['view_events'] = xarML('View Events');
    $menu['view_subscriptions'] = xarML('View Subscriptions');
    $menu['view_templates'] = xarML('View Templates');
    $menu['new_template'] = xarML('New Template');
    
    return $menu;

}

?>
