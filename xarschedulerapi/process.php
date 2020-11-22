<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2020 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Process the active reminders
 *
 */

function reminders_schedulerapi_process($args)
{
    $results = xarMod::apiFunc('reminders', 'admin', 'process');

    // Tell the scheduler that the job ran, but nothing was sent
    if (empty($results)) $results = xarML('No reminders sent');
    return $results;
}

?>
