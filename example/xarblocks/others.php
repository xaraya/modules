<?php
/**
 * File: $Id: s.others.php 1.8 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 * 
 * Example block initialisation
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */

/**
 * initialise block
 */
function example_othersblock_init()
{
    return true;
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
    if (!xarVarIsCached('Blocks.example', 'exid')) {
        // if not, we don't show this
        return;
    } 

    $current_exid = xarVarGetCached('Blocks.example', 'exid');
    if (empty($current_exid) || !is_numeric($current_exid)) {
        return;
    } 
    // Security check
    if (!xarSecurityCheck('ReadExampleBlock', 1, 'Block', $blockinfo['title'])) return; 
    // Get variables from content block
    $vars = @unserialize($blockinfo['content']); 
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 
    // Database information
    xarModDBInfoLoad('example');
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
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
    $output = new pnHTML(); 
    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name) = $result->fields;

        if (xarSecurityCheck('ViewExample', 0, 'Item', "$name:All:$exid")) {
            if (xarSecurityCheck('ReadExample', 0, 'Item', "$name:All:$exid")) {
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
    // Get current content
    $vars = @unserialize($blockinfo['content']); 
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 
    // Send content to template
    $output = xarTplBlock('example', 'othersAdmin', array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid'])); 
    // Return output
    return $output;
} 

/**
 * update block settings
 */
function example_othersblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'isset', $vars['numitems'], NULL, XARVAR_DONT_SET)) return;

    // Define a default block title
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Other example items');
    } 

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
} 

?>
