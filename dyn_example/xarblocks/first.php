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
function example_firstblock_init()
{
    // Security
    // xarSecAddSchema('Example:Firstblock:', 'Block title::');
    return true;
}

/**
 * get information on block
 */
function example_firstblock_info()
{
    // Values
    return array('text_type' => 'First',
                 'module' => 'example',
                 'text_type_long' => 'Show first example items (alphabetical)',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function example_firstblock_display($blockinfo)
{
    // Security check
    if (!xarSecAuthAction(0,
                         'Example:Firstblock:',
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
function example_firstblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    
    // Send content to template
    $output = xarTplBlock('example','firstAdmin', array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid']));

    // Return output
    return $output;
}

/**
 * update block settings
 */
function example_firstblock_update($blockinfo)
{
    $vars['numitems'] = xarVarCleanFromInput('numitems');

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>
