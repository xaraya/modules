<?php
// File: featureditems.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Err, well, this file was created by
// Jonn Beames, but it consist almost exlusively of code originally by
// Jim McDonald, MikeC, and Mike(of mikespub fame) taken from the
// topitems.php block of the articles module.  And Richard Cave gave me
// help with the multiselect box.
// Purpose of file: Featured Articles Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */

function articles_featureditemsblock_init()
{
    return array(
        'featuredaid' => 0,
        'alttitle' => '',
        'altsummary' => '',
        'toptype' => 'date',
        'moreitems' => array(),
        'showvalue' => true,
        'toptype' => 'date',
        'pubtypeid' => '',
        'catfilter' => '',
        'status' => array(3, 2),
        'itemlimit' => 10,
        'showfeaturedsum' => false,
        'moreitems' => array(),
        'showsummary' => false,
        'linkpubtype' => false,
        'linkcat' => false
    );
}


/**
 * get information on block
 */

function articles_featureditemsblock_info()
{
    // Details of block.
    return array(
        'text_type'         => 'Featured Items',
        'module'            => 'articles',
        'text_type_long'    => 'Show featured articles',
        'allow_multiple'    => true,
        'form_content'      => false,
        'form_refresh'      => false,
        'show_preview'      => true
    );
}

/**
 * display block
 */

function articles_featureditemsblock_display(& $blockinfo)
{
    // Security check
    // TODO: can be removed when handled centrally.
    if (!xarSecurityCheck('ReadArticlesBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block
    if (is_string($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars =& $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['featuredaid'])) {$vars['featuredaid'] = 0;}
    if (empty($vars['alttitle'])) {$vars['alttitle'] = '';}
    if (empty($vars['altsummary'])) {$vars['altsummary'] = '';}
    if (empty($vars['toptype'])) {$vars['toptype'] = 'date';}
    if (empty($vars['moreitems'])) {$vars['moreitems'] = array();}
    if (empty($vars['linkcat'])) {$vars['linkcat'] = false;}

    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'rating') {
            $vars['showvalue'] = false;
        } else {
            $vars['showvalue'] = true;
        }
    }

    $featuredaid = $vars['featuredaid'];

    $fields = array('aid', 'title');
    if ($vars['toptype'] == 'rating') {
        $fields[] = 'rating';
        $sort = 'rating';
    } elseif ($vars['toptype'] == 'hits') {
        $fields[] = 'counter';
        $sort = 'hits';
    } else {
        $fields[] = 'pubdate';
        $sort = 'date';
    }

    // Added the 'summary' field to the field list.
    if (!empty($vars['showsummary'])) {
        $fields[] = 'summary';
    }

    // Initialize arrays
    $data['feature'] = array();
    $data['items'] = array();

    // Setup featured item
    if ($featuredaid > 0) {
        $featuredart = xarModAPIFunc(
            'articles','user','get',
            array(
                'aid' => $featuredaid,
                'fields' => $fields
            )
        );

        $featuredlink = xarModURL(
            'articles', 'user', 'display',
            array(
                'aid' => $featuredart['aid'],
                'itemtype' => (!empty($vars['linkpubtype']) ? $featuredart['pubtypeid'] : NULL),
                'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
            )
        );

        $data['feature'][] = array(
            'featuredlabel'     => $featuredart['title'],
            'featuredlink'      => $featuredlink,
            'alttitle'          => $vars['alttitle'],
            'altsummary'        => $vars['altsummary'],
            'showfeaturedsum'   => $vars['showfeaturedsum'],
            'featureddesc'      => $featuredart['summary'] 
        );
    }

    // Setup additional items 
    if (!empty($vars['moreitems'])) {
        $articles = xarModAPIFunc(
            'articles', 'user', 'getall',
            array(
                'aids' => $vars['moreitems'],
                'enddate' => time(),
                'fields' => $fields,
                'sort' => $sort
            )
        );

        // See if we're currently displaying an article
        if (xarVarIsCached('Blocks.articles', 'aid')) {
            $curaid = xarVarGetCached('Blocks.articles', 'aid');
        } else {
            $curaid = -1;
        }

        foreach ($articles as $article) {
            if ($article['aid'] != $curaid) {
                $link = xarModURL(
                    'articles', 'user', 'display',
                    array (
                        'aid' => $article['aid'],
                        'itemtype' => (!empty($vars['linkpubtype']) ? $article['pubtypeid'] : NULL),
                        'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                    )
                );
            } else {
                $link = '';
            }

            if ($vars['showvalue']) {
                if ($vars['toptype'] == 'rating') {
                    $count = intval($article['rating']);
                } elseif ($vars['toptype'] == 'hits') {
                    $count = $article['counter'];
                } else {
                    // TODO: make user-dependent
                    if (!empty($article['pubdate'])) {
                        $count = strftime("%Y-%m-%d", $article['pubdate']);
                    } else {
                        $count = 0;
                    }
                }
            } else {
                $count = 0;
            }

            // Pass $desc to items[] array so that the block template can render it
            $data['items'][] = array(
                'label' => $article['title'],
                'link' => $link,
                'count' => $count,
                'desc' => (!empty($vars['showsummary']) ? $article['summary'] : '')
            );
        }
    }

    if (empty($data['feature']) && empty($data['items'])) {
        // Nothing to display.
        return;
    }

    // Set the data to return.
    $blockinfo['content'] = $data;
    return $blockinfo;
}

/**
 * built-in block help/information system.
 */

function articles_featureditemsblock_help()
{
    // No information yet.
    return '';
}

?>
