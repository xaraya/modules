<?php
/**
 * Articles module related articles block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 */
/**
 * initialise block
 */
function articles_relatedblock_init()
{
    return array(
        'numitems' => 5,
        'showvalue' => true,
        'nocache' => 1, // don't cache by default
        'pageshared' => 0, // don't share across pages
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
}

/**
 * get information on block
 */
function articles_relatedblock_info()
{
    // Values
    return array(
        'text_type' => 'Related',
        'module' => 'articles',
        'text_type_long' => 'Show related categories and author links',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function articles_relatedblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadArticlesBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    if (empty($vars['showvalue'])) {
        $vars['showvalue'] = false;
    }

    // Trick : work with cached variables here (set by the module function)

    // Check if we've been through articles display
    if (!xarVarIsCached('Blocks.articles','id')) {
        return;
    }

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    $links = 0;
    // Show publication type (for now)
    if (xarVarIsCached('Blocks.articles','ptid')) {
        $ptid = xarVarGetCached('Blocks.articles','ptid');
        if (!empty($ptid) && isset($pubtypes[$ptid]['descr'])) {
            $vars['pubtypelink'] = xarModURL('articles','user','view',
                                             array('ptid' => $ptid));
            $vars['pubtypename'] = $pubtypes[$ptid]['descr'];
            $links++;
        }
    }
    // Show categories (for now)
    if (xarVarIsCached('Blocks.articles','cids')) {
        $cids = xarVarGetCached('Blocks.articles','cids');
        // TODO: add related links
    }
    // Show author (for now)
    if (xarVarIsCached('Blocks.articles','authorid') &&
        xarVarIsCached('Blocks.articles','author')) {
        $authorid = xarVarGetCached('Blocks.articles','authorid');
        $author = xarVarGetCached('Blocks.articles','author');
        if (!empty($authorid) && !empty($author)) {
            $vars['authorlink'] = xarModURL('articles','user','view',
                                            array('ptid' => (!empty($ptid) ? $ptid : null),
                                                  'authorid' => $authorid));
            $vars['authorname'] = $author;
            $vars['authorid'] = $authorid;
            if (!empty($vars['showvalue'])) {
                $vars['authorcount'] = xarModAPIFunc('articles','user','countitems',
                                                     array('ptid' => (!empty($ptid) ? $ptid : null),
                                                           'authorid' => $authorid,
                                                           // limit to approved / frontpage articles
                                                           'status' => array(2,3),
                                                           'enddate' => time()));
            }
            $links++;
        }
    }

    $vars['blockid'] = $blockinfo['bid'];

    // Populate block info and pass to theme
    if ($links > 0) {
        $blockinfo['content'] = $vars;
        return $blockinfo;
    }

    return;
}


/**
 * modify block settings
 */
function articles_relatedblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    if (empty($vars['showvalue'])) {
        $vars['showvalue'] = false;
    }

    $vars['bid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
 * update block settings
 */
function articles_relatedblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('numitems', 'int:1:100', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showvalue', 'checkbox', $vars['showvalue'], false, XARVAR_NOT_REQUIRED)) {return;}

    $blockinfo['content'] = $vars;

    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function articles_relatedblock_help()
{
    return '';
}

?>
