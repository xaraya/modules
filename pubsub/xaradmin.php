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
    $data = pubsub_admin_menu();
    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarMLByKey('PUBSUBNAME'));
    $data['pager'] = '';

    if (!xarSecAuthAction(0, 'Pubsub::', '::', ACCESS_EDIT)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
    
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
