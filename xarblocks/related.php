<?php
/**
 * Publications module related publications block
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 */
/**
 * initialise block
 */
function publications_relatedblock_init()
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
function publications_relatedblock_info()
{
    // Values
    return array(
        'text_type' => 'Related',
        'module' => 'publications',
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
function publications_relatedblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadPublicationsBlock', 0, 'Block', $blockinfo['title'])) {return;}

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

    // Check if we've been through publications display
    if (!xarVarIsCached('Blocks.publications','id')) {
        return;
    }

    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

    $links = 0;
    // Show publication type (for now)
    if (xarVarIsCached('Blocks.publications','ptid')) {
        $ptid = xarVarGetCached('Blocks.publications','ptid');
        if (!empty($ptid) && isset($pubtypes[$ptid]['description'])) {
            $vars['pubtypelink'] = xarModURL('publications','user','view',
                                             array('ptid' => $ptid));
            $vars['pubtypename'] = $pubtypes[$ptid]['description'];
            $links++;
        }
    }
    // Show categories (for now)
    if (xarVarIsCached('Blocks.publications','cids')) {
        $cids = xarVarGetCached('Blocks.publications','cids');
        // TODO: add related links
    }
    // Show author (for now)
    if (xarVarIsCached('Blocks.publications','owner') &&
        xarVarIsCached('Blocks.publications','author')) {
        $owner = xarVarGetCached('Blocks.publications','owner');
        $author = xarVarGetCached('Blocks.publications','author');
        if (!empty($owner) && !empty($author)) {
            $vars['authorlink'] = xarModURL('publications','user','view',
                                            array('ptid' => (!empty($ptid) ? $ptid : null),
                                                  'owner' => $owner));
            $vars['authorname'] = $author;
            $vars['owner'] = $owner;
            if (!empty($vars['showvalue'])) {
                $vars['authorcount'] = xarModAPIFunc('publications','user','countitems',
                                                     array('ptid' => (!empty($ptid) ? $ptid : null),
                                                           'owner' => $owner,
                                                           // limit to approved / frontpage publications
                                                           'state' => array(2,3),
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
function publications_relatedblock_modify($blockinfo)
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
function publications_relatedblock_update($blockinfo)
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
function publications_relatedblock_help()
{
    return '';
}

?>
