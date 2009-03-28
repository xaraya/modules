<?php
/**
 * Modify Random Block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 *
 */
/**
 * modify block settings
 * @author Roger Keays
 */

function publications_randomblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['pubtype_id'])) {$vars['pubtype_id'] = '';}
    if (empty($vars['catfilter'])) {$vars['catfilter'] = '';}
    if (empty($vars['state'])) {$vars['state'] = array(3, 2);}
    if (empty($vars['locale'])) {$vars['locale'] = '';}
    if (empty($vars['numitems'])) {$vars['numitems'] = 5;}
    if (empty($vars['alttitle'])) {$vars['alttitle'] = '';}
    if (empty($vars['altsummary'])) {$vars['altsummary'] = '';}
    if (empty($vars['showtitle'])) {$vars['showtitle'] = false;}
    if (empty($vars['showsummary'])) {$vars['showsummary'] = false;}
    if (empty($vars['showpubdate'])) {$vars['showpubdate'] = false;}
    if (empty($vars['showauthor'])) {$vars['showauthor'] = false;}
    if (empty($vars['showsubmit'])) {$vars['showsubmit'] = false;}
    if (empty($vars['showdynamic'])) {$vars['showdynamic'] = false;}
    if (empty($vars['linkpubtype'])) {$vars['linkpubtype'] = false;}
    $vars['pubtypes'] = xarModAPIFunc('publications', 'user', 'get_pubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');
    $vars['stateoptions'] = array(
        array('id' => '', 'name' => xarML('All Published')),
        array('id' => '3', 'name' => xarML('Frontpage')),
        array('id' => '2', 'name' => xarML('Approved'))
    );
    if(!empty($vars['catfilter'])) {
        $cidsarray = array($vars['catfilter']);
    } else {
        $cidsarray = array();
    }

    $vars['locales'] = xarMLSListSiteLocales();
    asort($vars['locales']);

    $vars['blockid'] = $blockinfo['bid'];
    // Return output (template data)
    return $vars;
}

/**
 * update block settings
 */

function publications_randomblock_update($blockinfo)
{
    // Make sure we retrieve the new pubtype from the configuration form.
    // TODO: use xarVarFetch()
    xarVarFetch('pubtype_id', 'id', $vars['pubtype_id'], 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('catfilter', 'id', $vars['catfilter'], 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('state', 'int:0:4', $vars['state'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('locale', 'str', $vars['locale'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('alttitle', 'str', $vars['alttitle'], '', XARVAR_NOT_REQUIRED);
    xarVarFetch('altsummary', 'str', $vars['altsummary'], '', XARVAR_NOT_REQUIRED);
    if (!xarVarFetch('numitems', 'int:1:100', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
    xarVarFetch('showtitle', 'checkbox', $vars['showtitle'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showsummary', 'checkbox', $vars['showsummary'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showpubdate', 'checkbox', $vars['showpubdate'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showauthor', 'checkbox', $vars['showauthor'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showsubmit', 'checkbox', $vars['showsubmit'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('showdynamic', 'checkbox', $vars['showdynamic'], false, XARVAR_NOT_REQUIRED);
    xarVarFetch('linkpubtype', 'checkbox', $vars['linkpubtype'], false, XARVAR_NOT_REQUIRED);

    $vars['blockid'] = $blockinfo['bid'];
    $blockinfo['content'] = $vars;
    return $blockinfo;
}

?>
