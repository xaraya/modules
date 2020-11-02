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
 * Define the list of available scheduler triggers
 *
 * @author mikespub
 * @return array of intervals
 */
function scheduler_userapi_triggers()
{
    $triggers = array(
                       0 => xarML('Disabled'),
                       1 => xarML('External scheduler'),
                       2 => xarML('Scheduler block'),
                    //   3 => xarML('Event handler') not currently used
                      );

    return $triggers;
}
