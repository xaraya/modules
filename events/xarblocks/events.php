<?php
/**
 * File: $Id: s.events.php 1.8 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 *
 * Events Block
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
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
}

/**
 * get information on block
 */
function events_eventsblock_info()
{
    // Values
    return array('text_type' => 'Events',
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
    if (!xarSecAuthAction(0,
                         'Events:Eventsblock:',
                         "$blockinfo[title]::",
                         ACCESS_READ)) {
        return;
    }

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

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
    $output = new pnHTML();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name) = $result->fields;

        if (xarSecAuthAction(0,
                            'Events::',
                            "$name::$exid",
                            ACCESS_OVERVIEW)) {
            if (xarSecAuthAction(0,
                                'Events::',
                                "$name::$exid",
                                ACCESS_READ)) {
                $output->URL(xarModURL('events',
                                      'user',
                                      'display',
                                      array('exid' => $exid)),
                             $name);
            } else {
                $output->Text($name);
            }
            $output->Linebreak();
        }

    }
    $output->Linebreak();

// TODO: shouldn't this stuff be BL-able too ??
// Besides the fact that title & content are placed according to some
// master block template, why can't we create content via BL ?

    // Populate block info and pass to theme
    $blockinfo['content'] = $output->GetOutput();
    return $blockinfo;
}


/**
 * modify block settings
 */
function events_eventsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    
    // Send content to template
    $output = xarTplBlock('events','eventsAdmin', array('numitems' => $vars['numitems']));

    // Return output
    return $output;
}

/**
 * update block settings
 */
function events_eventsblock_update($blockinfo)
{
    $vars['numitems'] = xarVarCleanFromInput('numitems');

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>
