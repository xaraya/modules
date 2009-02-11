<?php
/**
 * Top Items Block
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
 * @author Jim McDonald
 */
function publications_topitemsblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    if (!isset($vars['linkpubtype'])) {
        $vars['linkpubtype'] = true;
    }
    if (!isset($vars['includechildren'])) {
        $vars['includechildren'] = false;
    }
    if (!isset($vars['linkcat'])) {
        $vars['linkcat'] = false;
    }

    $vars['pubtypes'] = xarModAPIFunc('publications', 'user', 'getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');

    $vars['sortoptions'] = array(
        array('id' => 'hits', 'name' => xarML('Hit Count')),
        array('id' => 'rating', 'name' => xarML('Rating')),
        array('id' => 'date', 'name' => xarML('Date'))
    );

    $vars['stateoptions'] = array(
        array('id' => '2,3', 'name' => xarML('All Published')),
        array('id' => '3', 'name' => xarML('Frontpage')),
        array('id' => '2', 'name' => xarML('Approved'))
    );

    $vars['blockid'] = $blockinfo['bid'];
    // Return output
    return $vars;
}

/**
 * update block settings
 * @author Jim McDonald
 */
function publications_topitemsblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:1:200', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('pubtype_id', 'id', $vars['pubtype_id'], 0, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('linkpubtype', 'checkbox', $vars['linkpubtype'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('nopublimit', 'checkbox', $vars['nopublimit'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('catfilter', 'id', $vars['catfilter'], 0, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('includechildren', 'checkbox', $vars['includechildren'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('nocatlimit', 'checkbox', $vars['nocatlimit'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('linkcat', 'checkbox', $vars['linkcat'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('dynamictitle', 'checkbox', $vars['dynamictitle'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('toptype', 'enum:hits:rating:date', $vars['toptype'])) {return;}
    if (!xarVarFetch('showsummary', 'checkbox', $vars['showsummary'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showdynamic', 'checkbox', $vars['showdynamic'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showvalue', 'checkbox', $vars['showvalue'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('state', 'strlist:,:int:1:4', $vars['state'])) {return;}

    if ($vars['nopublimit'] == true) {
        $vars['pubtype_id'] = 0;
    }
    if ($vars['nocatlimit'] == true) {
        $vars['catfilter'] = 0;
        $vars['includechildren'] = false;
    }
    if ($vars['includechildren'] == true) {
        $vars['linkcat'] = false;
    }

    $blockinfo['content'] = $vars;

    return $blockinfo;
}

?>