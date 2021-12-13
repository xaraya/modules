<?php
/**
 * @package modules
 * @subpackage reminders
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/771.html
 */

/**
 * Process a raw email supplied to use by some gateway (ws.php for example)
 *
 * This function is now simple, but not smart. Ideally we want to do what we
 * do below very quickly to prevent real-time lock-ups.
 * In other words, the current code assumes we dont get many mails :-)
 *
 * @return integer exitcode to gateway script
 * @todo what do we do with security here?
 */
function reminders_cliapi_process_reminders(array $args=[])
{
    xarLog::message("Reminders: processing the reminders queue", xarLog::LEVEL_DEBUG);
    extract($args);
    assert($argc > 0 && $argv[1] == "reminders");

    // TODO: Guess ;-)
    if (isset($argv[2]) && $argv[2]=='-u') {
        $user = $argv[3];
    }
    if (isset($argv[4]) && $argv[4]=='-p') {
        $pass = $argv[5];
    }
    if (!isset($user) or !isset($pass)) {
        echo "Usage: reminders -u <user> -p <pass> [reminderscontent]\n";
        return 1;
    }
    if (!xarUser::login($user, $pass)) {
        echo "Authentication failed\n";
        return 1;
    }

    // Authentication OK. Run the reminders process
    xarMod::apiFunc('reminders', 'admin', 'process_reminders');

    return true;
}
