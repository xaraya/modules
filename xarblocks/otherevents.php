<?php
/**
 * Events Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage events
 * @author Events module development team 
 */

/**
 * initialise block
 */
function events_othersblock_init()
{
    return array(
        'numitems' => 5
    );
} 

/**
 * get information on block
 */
function events_othersblock_info()
{ 
    // Values
    return array(
        'text_type' => 'Others',
        'module' => 'events',
        'text_type_long' => 'Show other events items when 1 is displayed',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
} 

/**
 * display block
 */
function events_othersblock_display($blockinfo)
{ 
    // See if we are currently displaying an events item
    // (this variable is set in the user display function)
    if (!xarVarIsCached('Blocks.events', 'eventid')) {
        // if not, we don't show this
        return;
    } 

    $current_eventid = xarVarGetCached('Blocks.events', 'eventid');
    if (empty($current_eventid) || !is_numeric($current_eventid)) {
        return;
    } 

    // Security check
    if (!xarSecurityCheck('OverviewEvents', 0, 'Event', $blockinfo['title'])) {return;}

    // Get variables from content block.
    // Content is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
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
    $xartable =& xarDBGetTables();
    $eventstable = $xartable['events']; 

    // Query
    $sql = "SELECT xar_eventid, xar_name
            FROM $eventstable
            WHERE xar_eventid != $current_eventid
            ORDER by xar_eventid DESC";
    $result = $dbconn->SelectLimit($sql, $vars['numitems']);

    if ($dbconn->ErrorNo() != 0) {
        return;
    } 

    if ($result->EOF) {
        return;
    } 

    // Create output object
    $items = array();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($eventid, $name) = $result->fields;

        if (xarSecurityCheck('ViewEvents', 0, 'Item', "$name:All:$eventid")) {
            if (xarSecurityCheck('ReadEvents', 0, 'Item', "$name:All:$eventid")) {
                $item = array();
                $item['link'] = xarModURL(
                    'events', 'user', 'display',
                    array('eventid' => $eventid)
                );
                
            }
            $item['name'] = $name;
        } 
        $items[] = $item;
    } 

    $blockinfo['content'] = array('items' => $items);

    return $blockinfo;
} 

?>