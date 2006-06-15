<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file: Example Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function example_othersblock_init()
{
    // Security
    xarSecAddSchema('Example:Othersblock:', 'Block title::');
}

/**
 * get information on block
 */
function example_othersblock_info()
{
    // Values
    return array('text_type' => 'Others',
                 'module' => 'example',
                 'text_type_long' => 'Show other example items when 1 is displayed',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function example_othersblock_display($blockinfo)
{

    // See if we are currently displaying an example item
    // (this variable is set in the user display function)
    if (!xarVarIsCached('Blocks.example','exid')) {
        // if not, we don't show this
        return;
    }

    $current_exid = xarVarGetCached('Blocks.example','exid');
    if (empty($current_exid) || !is_numeric($current_exid)) {
        return;
    }

    // Security check
    if (!xarSecAuthAction(0,
                         'Example:Othersblock:',
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
    xarModDBInfoLoad('example');
    $dbconn =& xarDBGetConn();
    $xartable =xarDBGetTables();
    $exampletable = $xartable['example'];

    // Query
    $sql = "SELECT xar_exid,
                   xar_name
            FROM $exampletable
            WHERE xar_exid != '" . xarVarPrepForStore($current_exid) . "'
            ORDER by xar_exid DESC";
    $result = $dbconn->SelectLimit($sql, $vars['numitems']);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }
    // Create output object
    $output = new xarHTML();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name) = $result->fields;

        if (xarSecAuthAction(0,
                            'Example::',
                            "$name::$exid",
                            ACCESS_OVERVIEW)) {
            if (xarSecAuthAction(0,
                                'Example::',
                                "$name::$exid",
                                ACCESS_READ)) {
                $output->URL(xarModURL('example',
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
function example_othersblock_modify($blockinfo)
{
    // Create output object
    $output = new xarHTML();

    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    // Create row
    $row = array();
    $output->SetOutputMode(_XH_RETURNOUTPUT);
    $row[] = $output->Text(xarML('Number of example tasks to display'));
    $row[] = $output->FormText('numitems',
                               xarVarPrepForDisplay($vars['numitems']),
                               5,
                               5);
    $output->SetOutputMode(_XH_KEEPOUTPUT);

    // Add row
    $output->SetInputMode(_XH_VERBATIMINPUT);
    $output->TableAddRow($row, 'left');
    $output->SetInputMode(_XH_PARSEINPUT);

    // Return output
    return $output->GetOutput();
}

/**
 * update block settings
 */
function example_othersblock_update($blockinfo)
{
    $vars['numitems'] = xarVarCleanFromInput('numitems');

    // Define a default block title
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Other example items');
    }

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function example_othersblock_help()
{
    $output = new xarHTML();

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $output->Text('Any related block info should be placed in your modname_blocknameblock_help() function.');
    $output->LineBreak(2);
    $output->Text('More information.');
    $output->SetInputMode(_XH_PARSEINPUT);

    return $output->GetOutput();
}
?>