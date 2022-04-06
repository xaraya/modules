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
 * Call a generic test page
 *
 */
function reminders_user_test()
{
    $rows = xarMod::apiFunc('reminders','scheduler','process_lookups');
//    $rows = xarMod::apiFunc('reminders','admin','generate_random_entry', array('user' => 3));
//    var_dump($rows);
    return array();
}
?>
