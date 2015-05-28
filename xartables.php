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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Table function
 *
 * @access public
 * @param none
 * @returns bool
 * @throws DATABASE_ERROR
*/
function pubsub_xartables()
{
    // Initialise table array
    $xartable = array();

    $xartable['pubsub_events']        = xarDB::getPrefix() . '_pubsub_events';
    $xartable['pubsub_subscriptions'] = xarDB::getPrefix() . '_pubsub_subscriptions';
    $xartable['pubsub_process']       = xarDB::getPrefix() . '_pubsub_process';
    $xartable['pubsub_templates']     = xarDB::getPrefix() . '_pubsub_templates';

    return $xartable;
}

?>
