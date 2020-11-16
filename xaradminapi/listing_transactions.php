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
 * Handle calls from the listing property
 *
 */

function payments_adminapi_listing_transactions($args)
{
    return xarMod::guiFunc('payments', 'user', 'create_20022_file');
}
