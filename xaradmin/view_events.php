<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Displays a summary of category subscriptions and basic metrics. Provides options
 * to view details about each subscription
 */
function pubsub_admin_view_events()
{
    if (!xarSecurity::check('ManagePubSub')) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'pubsub_events'));
    
    $data['items'] = array();
    $data['authid'] = xarSec::genAuthKey();


    // The user API function is called
//    $events = xarMod::apiFunc('pubsub', 'user', 'getall');
    $events = $data['object']->getItems();

    $data['items'] = $events;

    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';

    // return the template variables defined in this template

    return $data;
}

?>
