<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Handle getconfig hook calls
 *
 */

function payments_adminapi_get_message_identifier($args)
{
    if (isset($args['id']) && is_numeric($args['id'])) {
        $id = $args['id'];
    } else {
        $id = (int)xarModVars::get('payments', 'message_id');
    }
    xarModVars::set('payments', 'message_id', $id + 1);

    $prefix = xarModVars::get('payments', 'message_prefix');
    if (!empty($prefix)) {
        $identifier = $prefix . "-" . $id;
    } else {
        $identifier = $id;
    }
    $identifier = xarMod::apiFunc('payments', 'admin', 'check_swift_char', ['string' => $identifier]);
    return $identifier;
}
