<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
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
