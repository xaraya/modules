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
    $data['modnamelabel'] = xarVarPrepForDisplay(xarMLByKey('Module Name'));
    $data['categorylabel'] = xarVarPrepForDisplay(xarMLByKey('Category'));
    $data['itemlabel'] = xarVarPrepForDisplay(xarMLByKey('Item'));
    $data['numsubscriberslabel'] = xarVarPrepForDisplay(xarMLByKey('Number of Subscribers'));
    $data['templatelabel'] = xarVarPrepForDisplay(xarMLByKey('Template'));
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
    
    $data['items'] = $events;

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';
    
    // return the template variables defined in this template
    return $data;   
}
?>
