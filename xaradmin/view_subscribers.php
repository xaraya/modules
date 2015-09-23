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
 * Displays a list of subscribers to a given category. Provides an option
 * to manually remove a subscriber.
 */
function pubsub_admin_view_subscribers(){
	    if (!xarSecurityCheck('ManagePubSub')) return;
	    xarTplSetPageTitle('View Subscribers');
	
	    $modulename = 'pubsub';
	
	    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'pubsub_subscriptions'));
	   	$q = $data['object']->dataquery;
	    
		// Only active domains
		$q->eq('state', 3);
	    return $data;
    }
?>
