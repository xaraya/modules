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
 *
 * Table information
 *
 */

function reminders_xartables()
{
    // Initialise table array
    $xartable = array();

    $xartable['reminders_emails']          = xarDB::getPrefix() . '_reminders_emails';
    $xartable['reminders_entries']          = xarDB::getPrefix() . '_reminders_entries';

    // Return the table information
    return $xartable;
}
?>