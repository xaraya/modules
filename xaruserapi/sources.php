<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author mikespub
 */
/**
 * Define the list of available trigger source types
 *
 * @author mikespub
 * @return array of intervals
 */
function scheduler_userapi_sources()
{
    $triggers = array(
                       1 => xarML('Localhost'),
                       2 => xarML('IP address (direct connection)'),
                       3 => xarML('IP address (behind proxy)'),
                       4 => xarML('Host name')
                      );

    return $triggers;
}

?>
