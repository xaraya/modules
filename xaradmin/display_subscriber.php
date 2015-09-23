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
 * Display an item of the pubsub_subscriptions object
 *
 */
sys::import('modules.dynamicdata.class.objects.master');

function pubsub_admin_display_subscriber()
{
    // Xaraya security
    if (!xarSecurityCheck('ManagePubSub')) return;
    xarTpl::setPageTitle('Display Subscriber');

    if (!xarVarFetch('name',       'str',    $name,            'pubsub_subscriptions', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

    
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));
	
    $data['tplmodule'] = 'pubsub';
    
    return $data;
}
?>