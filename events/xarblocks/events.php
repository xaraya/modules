<?php
/**
 * Events Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage events
 * @author Events module development team
 */

/**
 * initialise block
 */
function events_eventsblock_init()
{
    // Security
    xarSecAddSchema('Events:Eventsblock:', 'Block title::');
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 */
function events_eventsblock_info()
{
    // Values
    return array(
        'text_type' => 'Events',
        'module' => 'events',
        'text_type_long' => 'Show events events items (alphabetical)',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
         'show_preview' => true);
}

/**
 * display block
 */
function events_eventsblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('OverviewEvents', 0, 'Event', $blockinfo['title'])) {return;}

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    // Database information
    xarModDBInfoLoad('events');
    $dbconn =& xarDBGetConn();
    $xartable =xarDBGetTables();
    $eventstable = $xartable['events'];

    // Query
    $sql = "SELECT xar_exid,
                   xar_name
            FROM $eventstable
            ORDER by xar_name";
    $result = $dbconn->SelectLimit($sql, $vars['numitems']);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }

    // Create output object
    $data['items'] = array();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($eventid, $name) = $result->fields;
        if (!xarSecurityCheck('OverviewEvents', 0, 'Event', "$name:All:$eventid")) {
            if (!xarSecurityCheck('ReadEvents', 0, 'Event', "$name:All:$eventid")) {
                $item['link'] = xarModURL(
                    'events', 'user', 'display',
                    array('eventid' => $item['eventid'])
                );
                // Security check 2 - else only display the item name (or whatever is
                // appropriate for your module)
            } else {
                $item['link'] = '';
            }
            // Add this item to the list of items to be displayed
            $data['items'][] = $item;
        }
    }

    $data['blockid'] = $blockinfo['bid'];

    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;
            
    return $blockinfo;
}

?>