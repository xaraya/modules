<?php
// File: modify-keywordsarticles.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Camille Perinel
// Mostly taken from the topitems.php block of the articles module.(See credits)
// Purpose of file: Keyword Articles Block
// ----------------------------------------------------------------------

/**
 * modify block settings
 */
function keywords_keywordsarticlesblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    $vars = _keywords_keywordsarticlesblock_checkdefaults($vars);

    $vars['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories','user','getcat');
    $vars['statusoptions'] = array(array('id' => '3,2',
                                         'name' => xarML('All Published')),
                                   array('id' => '3',
                                         'name' => xarML('Frontpage')),
                                   array('id' => '2',
                                         'name' => xarML('Approved'))
                                  );

    $vars['blockid'] = $blockinfo['bid'];
    // Return output
    return $vars;
}

/**
 * update block settings
 */
function keywords_keywordsarticlesblock_update($blockinfo)
{
    //MikeC: Make sure we retrieve the new pubtype from the configuration form.
    list($vars['ptid'],
         $vars['cid'],
         $vars['status'],
         $vars['refreshtime']
                           ) = xarVarCleanFromInput('ptid',
                                                    'cid',
                                                    'status',
                                                    'refreshtime'
                                                   );

    $vars = _keywords_keywordsarticlesblock_checkdefaults($vars);
    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * Makes sure all the required variables are set to display or modify the block
 */
function _keywords_keywordsarticlesblock_checkdefaults($vars)
{
    if (empty($vars['ptid'])) {
        $vars['ptid'] = '';
    }

    if (empty($vars['cid'])) {
        $vars['cid'] = '';
    }

    if (empty($vars['status'])) {
        $vars['status'] = '3,2';
    }

    /* don't use empty() because 0 is a valid value */
    if (!array_key_exists('refreshtime', $vars) ||
        !isset($vars['refreshtime'])) {
        $vars['refreshtime'] = 1440; // one day
    }

    return $vars;
}


?>
