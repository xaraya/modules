<?php
// File: modify-keywordsarticles.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Camille Perinel
// Mostly taken from the topitems.php block of the articles module.(See credits)
// Purpose of file: Keyword Categories Block
// ----------------------------------------------------------------------

/**
 * modify block settings
 */
function keywords_keywordscategoriesblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    $vars = _keywords_keywordscategoriesblock_checkdefaults($vars);

    $vars['blockid'] = $blockinfo['bid'];
    // Return output
    return $vars;
}

/**
 * update block settings
 */
function keywords_keywordscategoriesblock_update($blockinfo)
{
    //MikeC: Make sure we retrieve the new pubtype from the configuration form.
    $vars['refreshtime'] = xarVarCleanFromInput('refreshtime');
    $vars = _keywords_keywordscategoriesblock_checkdefaults($vars);
    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * Makes sure all the required variables are set to display or modify the block
 */
function _keywords_keywordscategoriesblock_checkdefaults($vars)
{
    if (empty($vars['cid'])) {
        $vars['cid'] = '';
    }
    /* don't use empty() because 0 is a valid value */
    if (!array_key_exists('refreshtime', $vars) ||
        !isset($vars['refreshtime'])) {
        $vars['refreshtime'] = 1440; // one day
    }
    return $vars;
}


?>
