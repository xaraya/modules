<?php
/**
 * Random Block
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
 * initialise block
 * @author Roger Keays
 */
function publications_randomblock_init()
{
    // Default values to initialize the block.
    return array(
        'pubtype_id'     => 0,
        'catfilter'     => 0,
        'state'        => '3,2',
        'locale'      => '',
        'numitems'      => 1,
        'alttitle'      => '',
        'altsummary'    => '',
        'showtitle'     => true,
        'showsummary'   => true,
        'showpubdate'   => false,
        'showsubmit'    => false,
        'showdynamic'   => false,
        'nocache'       => 0, // Cache this block
        'pageshared'    => 1, // Share across all pages
        'usershared'    => 1, // Share between group members
        'cacheexpire'   => null, // Default cache expiration time
        'linkpubtype'   => false
    );
}

/**
 * get information on block
 */
function publications_randomblock_info()
{
    // Return details about this block.
    return array(
        'text_type' => 'Random article',
        'module' => 'publications',
        'text_type_long' => 'Show a single random article',
        'allow_multiple' => true,
        'form_content' => false, // Deprecated
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function publications_randomblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadPublicationsBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from block content.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // frontpage or approved state
    if (empty($vars['state'])) {
            $statearray = array(2,3);
    } elseif (!is_array($vars['state'])) {
            $statearray = split(',', $vars['state']);
    } else {
            $statearray = $vars['state'];
    }

    if (empty($vars['locale'])) {
        $lang = null;
    } elseif ($vars['locale'] == 'current') {
        $lang = xarMLSGetCurrentLocale();
    } else {
        $lang = $vars['locale'];
    }

    // get cids for security check in getall
    $fields = array('id', 'title', 'body', 'notes', 'pubtype_id', 'cids', 'owner');

    if (!empty($vars['showpubdate'])) {
        array_push($fields, 'pubdate');
    }
    if (!empty($vars['showsummary'])) {
        array_push($fields, 'summary');
    }
    if (!empty($vars['showauthor'])) {
        array_push($fields, 'author');
    }
    if (!empty($vars['alttitle'])) {
        $blockinfo['title'] = $vars['alttitle'];
    }
    if (empty($vars['pubtype_id'])) {
        $vars['pubtype_id'] = 0;
    }

    if (!empty($vars['catfilter'])) {
        // use admin defined category
        $cidsarray = array($vars['catfilter']);
        $cid = $vars['catfilter'];
    } else {
        $cid = 0;
        $cidsarray = array();
    }

    // check if dynamicdata is hooked for all pubtypes or the current one (= defaults to 0 anyway here)
    if (!empty($vars['showdynamic']) && xarModIsHooked('dynamicdata', 'publications', $vars['pubtype_id'])) {
        array_push($fields, 'dynamicdata');
    }

    if (empty($vars['numitems'])) $vars['numitems'] = 1;

    $publications = xarModAPIFunc('publications','user','getrandom',
                              array('ptid'     => $vars['pubtype_id'],
                                    'cids'     => $cidsarray,
                                    'andcids'  => false,
                                    'state'   => $statearray,
                                    'locale' => $lang,
                                    'numitems' => $vars['numitems'],
                                    'fields'   => $fields,
                                    'unique'   => true));

    if (!isset($publications) || !is_array($publications) || count($publications) == 0) {
        return;
    } else {
        foreach (array_keys($publications) as $key) {
            // for template compatibility :-(
            if (!empty($publications[$key]['author']) && !empty($vars['showauthor'])) {
                $publications[$key]['authorname'] = $publications[$key]['author'];
            }
            $vars['items'][] = $publications[$key];
        }
    }

    // Pass details back for rendering.
    if (count($vars['items']) > 0) {
        $blockinfo['content'] = $vars;
        return $blockinfo;
    }

    // Nothing to render.
    return;
}
/**
 * built-in block help/information system.
 */
function publications_randomblock_help()
{
    // No information yet.
    return '';
}

?>
