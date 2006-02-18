<?php
/**
 * Keywords Module Categories Block
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
 * @return array with vars
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
 * @return array blockinfo
 */
function keywords_keywordscategoriesblock_update($blockinfo)
{
    //MikeC: Make sure we retrieve the new pubtype from the configuration form.
    if (!xarVarFetch('refreshtime', 'int:1:', $vars['refreshtime'],1,XARVAR_DONT_SET)) return;
    $vars = _keywords_keywordscategoriesblock_checkdefaults($vars);
    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * Makes sure all the required variables are set to display or modify the block
 * @return array vars
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
