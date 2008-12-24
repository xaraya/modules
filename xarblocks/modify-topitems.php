<?php
/**
 * Top Items Block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 *
 */
/**
 * modify block settings
 * @author Jim McDonald
 */
function articles_topitemsblock_modify($blockinfo)
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

    $vars['pubtypes'] = xarModAPIFunc('articles', 'user', 'getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');

    $vars['sortoptions'] = array(
        array('id' => 'hits', 'name' => xarML('Hit Count')),
        array('id' => 'rating', 'name' => xarML('Rating')),
        array('id' => 'date', 'name' => xarML('Date'))
    );

    $vars['statusoptions'] = array(
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
function articles_topitemsblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'int:1:200', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('pubtypeid', 'id', $vars['pubtypeid'], 0, XARVAR_NOT_REQUIRED)) {return;}
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
    if (!xarVarFetch('status', 'strlist:,:int:1:4', $vars['status'])) {return;}

    if ($vars['nopublimit'] == true) {
        $vars['pubtypeid'] = 0;
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