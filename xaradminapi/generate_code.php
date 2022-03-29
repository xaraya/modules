<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

/**
 * Generate a unique code for each entry or lookup
 * The code is independent of the itemid
 */

function reminders_adminapi_generate_code($args)
{
    // Support both objects and arrays
    if (!empty($args['object'])) {
        // Get the raw values of this object
        $fields = $args['object']->getFieldValues([], 1);
    } else {
        $fields = $args['array'];
    }

    // CHECKME: think of something better here
    // Should use the item's data?
    $code = MD5(xarUser::getVar('id') . time());

    return $code;
}
