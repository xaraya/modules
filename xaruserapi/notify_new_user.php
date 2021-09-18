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
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

function pubsub_userapi_notify_new_user($args)
{
    // Send Welcome mail to the subscribed user

    $mail_data = [];
    $mail_data['header']       = xarML('Thanks to subscribing at #(1)', xarModVars::get('themes', 'SiteName'));
    $mail_data['footer']       = xarML('Xaraya #(1) Module', UCFirst(xarMod::getName()));
    $mail_data['title']        = date('r');

    $mailargs = [
                  'name'             => 'pubsub_welcome',
                  'sendername'       => xarModVars::get('pubsub', 'defaultsendername'),
                  'senderaddress'    => xarModVars::get('pubsub', 'defaultsenderaddress'),
                  'subject'          => xarML('You have subscribed at #(1)', xarModVars::get('themes', 'SiteName')),
                  'recipientname'    => xarModVars::get('themes', 'SiteName'),
                  'recipientaddress' => $user['email'],
                  'data'             => $args['mail_data'],
    ];

    $result = xarMod::apiFunc('mailer', 'user', 'send', $mailargs);

    // Notify the admin if required
    if (xarModVars::get('pubsub', 'sendnotice_subscription')) {
        $admin = xarRoles::getRole(xarModVars::get('roles', 'admin'));

        $mail_data = [];
        $mail_data['message_type'] = 'subscription';
        $mail_data['count']        = $result;
        $mail_data['header']       = xarML('Notification from #(1)', xarModVars::get('themes', 'SiteName'));
        $mail_data['footer']       = xarML('Xaraya #(1) Module', UCFirst(xarMod::getName()));
        $mail_data['title']        = date('r');
        $mail_data['name']         = $admin->properties['name']->value;

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
        $result = xarMod::apiFunc('mailer', 'user', 'send', $mailargs);
    }
}
