<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Table function
 *
 * @access public
 * @param none
 * @returns bool
 * @raise DATABASE_ERROR
*/
function pubsub_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for pubsub events database entities
    $pubsub_events = xarDBGetSiteTablePrefix() . '_pubsub_events';

    // Table name
    $xartable['pubsub_events'] = $pubsub_events;

// Note : this table is no longer in use - leave in here to handle upgrades
    // Name for pubsub event category ids database entities
    $pubsub_eventcids = xarDBGetSiteTablePrefix() . '_pubsub_eventcids';

    // Table name
    $xartable['pubsub_eventcids'] = $pubsub_eventcids;

    // Name for pubsub event registration database entities
    $pubsub_reg = xarDBGetSiteTablePrefix() . '_pubsub_reg';

    // Table name
    $xartable['pubsub_reg'] = $pubsub_reg;

    // Name for pubsub event handling database entities
    $pubsub_process = xarDBGetSiteTablePrefix() . '_pubsub_process';

    // Table name
    $xartable['pubsub_process'] = $pubsub_process;

    // Name for pubsub templates
    $pubsub_templates = xarDBGetSiteTablePrefix() . '_pubsub_templates';

    // Table name
    $xartable['pubsub_templates'] = $pubsub_templates;

    // Return table information
    return $xartable;
}

?>
