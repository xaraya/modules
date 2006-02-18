<?php
/**
 * Keywords Module Articles Block
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Original Author of file: Camille Perinel
 * Mostly taken from the topitems.php block of the articles module.(See credits)
 */
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

    if (!xarVarFetch('ptid', 'id', $vars['ptid'],NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('cid', 'int:1:', $vars['cid'],NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('status', 'str:1:', $vars['status'], XARVAR_DONT_SET)) return;
    if (!xarVarFetch('refreshtime', 'int:1:', $vars['refreshtime'],1,XARVAR_DONT_SET)) return;

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
