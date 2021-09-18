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
 * View the current event queue
 */
function pubsub_admin_view_queue($args)
{
    if (!xarSecurity::check('ManagePubSub')) {
        return;
    }

    extract($args);
    if (!xarVar::fetch('action', 'str', $action, '')) {
        return;
    }
    if (!xarVar::fetch('id', 'int', $id, 0)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(['name' => 'pubsub_process']);

    if (!empty($action) && ($action == 'process')) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        $result = xarMod::apiFunc('pubsub', 'admin', 'process_queue');

        // Notify the admin if required
        if (xarModVars::get('pubsub', 'sendnotice_queue')) {
            $admin = xarRoles::getRole(xarModVars::get('roles', 'admin'));

            $args['mail_data']['message_type'] = 'queue';
            $args['mail_data']['count']        = $result;
            $args['mail_data']['header']       = xarML('Notification from #(1)', xarModVars::get('themes', 'SiteName'));
            $args['mail_data']['footer']       = xarML('Xaraya #(1) Module', UCFirst(xarMod::getName()));
            $args['mail_data']['title']        = date('r');
            $args['mail_data']['name']         = $admin->properties['name']->value;

            $mailargs = [
                      'name'             => 'pubsub_admin',
                      'sendername'       => xarModVars::get('pubsub', 'defaultsendername'),
                      'senderaddress'    => xarModVars::get('pubsub', 'defaultsenderaddress'),
                      'subject'          => xarML('Notifications from #(1)', xarModVars::get('themes', 'SiteName')),
                      'recipientname'    => $admin->properties['name']->value,
                      'recipientaddress' => $admin->properties['email']->value,
                      'bccaddresses'     => [],
                      'data'             => $args['mail_data'],
            ];
            $data['result'] = xarMod::apiFunc('mailer', 'user', 'send', $mailargs);
        }

        xarController::redirect(xarController::URL('pubsub', 'admin', 'view_queue'));
        return true;
    }
    return $data;
}
