<?php
// File: $Id: topitems.php 1.34 03/11/20 19:12:15-08:00 jbeames@lxwdev-1.schwabfoundation.org $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file: Articles Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function articles_topitemsblock_init()
{
    // Initial values when the block is created.
    return serialize(array(
        'numitems' => 5,
        'pubtypeid' => 0,
        'nopublimit' => false,
        'catfilter' => 0,
        'nocatlimit' => true,
        'dynamictitle' => true,
        'toptype' => 'hits',
        'showvalue' => true,
        'showsummary' => false,
        'showdynamic' => false,
        'status' => '2,3'
    ));
}

/**
 * get information on block
 */
function articles_topitemsblock_info()
{
    // Values
    return array(
        'text_type' => 'Top Items',
        'module' => 'articles',
        'text_type_long' => 'Show top articles',
        'allow_multiple' => true,
        'form_content' => false, // Deprecated.
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function articles_topitemsblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadArticlesBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // see if we're currently displaying an article
    if (xarVarIsCached('Blocks.articles', 'aid')) {
        $curaid = xarVarGetCached('Blocks.articles', 'aid');
    } else {
        $curaid = -1;
    }
    
    if (!empty($vars['dynamictitle'])) {
        if ($vars['toptype'] == 'rating') {
            $blockinfo['title'] = xarML('Top Rated');
        } elseif ($vars['toptype'] == 'hits') {
            $blockinfo['title'] = xarML('Top');
        } else {
            $blockinfo['title'] = xarML('Latest');
        }
    }

    if (!empty($vars['nocatlimit'])) {
        // don't limit by category
        $cid = 0;
        $cidsarray = array();
    } else {
        if (!empty($vars['catfilter'])) {
            // use admin defined category 
            $cidsarray = array($vars['catfilter']);
            $cid = $vars['catfilter'];
        } else {
            // use the current category
            // Jonn: this currently only works with one category at a time
            // it could be reworked to support multiple cids
            if (xarVarIsCached('Blocks.articles', 'cids')) {
                $curcids = xarVarGetCached('Blocks.articles', 'cids');
                if (!empty($curcids)) {
                    if ($curaid == -1) {
                        //$cid = $curcids[0]['name'];
                        $cid = $curcids[0];
                        $cidsarray = $curcids;
                    } else {
                        $cid = $curcids[0];
                        $cidsarray = array($curcids[0]);
                    }
                } else {
                    $cid = 0;
                    $cidsarray = array();
                }
            } else {
                // pull from all categories
                $cid = 0;
                $cidsarray = array();
            }
        }

        if (!empty($cid)) {
            // if we're viewing all items below a certain category, i.e. catid = _NN
            $cid = preg_replace('/_/','',$cid);
            $thiscategory = xarModAPIFunc(
                'categories','user','getcat',
                array('cid' => $cid, 'return_itself' => 'return_itself')
            );
        }
        if ((!empty($cidsarray)) && (isset($thiscategory[0]['name'])) && !empty($vars['dynamictitle'])) {
            $blockinfo['title'] .= ' ' . $thiscategory[0]['name'];
        }
    }

	// Get publication types
    // MarieA - moved to always get pubtypes.
	$pubtypes = xarModAPIFunc('articles', 'user', 'getpubtypes');

    if (!empty($vars['nopublimit'])) {
        //don't limit by pubtype
        $ptid = 0;
        if (empty($vars['nocatlimit']) || empty($vars['nopublimit']) || !empty($vars['dynamictitle'])) {
            $blockinfo['title'] .= ' ' . xarML('Content');
        }
    } else {
        // MikeC: Check to see if admin has specified that only a specific 
        // Publication Type should be displayed.  If not, then default to original TopItems configuration.
        if ($vars['pubtypeid'] == 0)
        {
            if (xarVarIsCached('Blocks.articles', 'ptid')) {
                $ptid = xarVarGetCached('Blocks.articles', 'ptid');
            }
            if (empty($ptid)) {
                // default publication type
                $ptid = xarModGetVar('articles', 'defaultpubtype');
            }
        } else {
            // MikeC: Admin Specified a publication type, use it.
            $ptid = $vars['pubtypeid'];
        }
        
        if (!empty($ptid) && isset($pubtypes[$ptid]['descr']) && !empty($vars['dynamictitle'])) {
            $blockinfo['title'] .= ' ' . xarVarPrepForDisplay($pubtypes[$ptid]['descr']);
        }
    }

    // frontpage or approved status
    if (empty($vars['status'])) {
        $statusarray = array(2,3);
    } elseif (!is_array($vars['status'])) {
        $statusarray = split(',', $vars['status']);
    } else {
        $statusarray = $vars['status'];
    }

    // get cids for security check in getall
    $fields = array('aid', 'title', 'pubtypeid', 'cids');
    if ($vars['toptype'] == 'rating') {
        array_push($fields, 'rating');
        $sort = 'rating';
    } elseif ($vars['toptype'] == 'hits') {
        array_push($fields, 'counter');
        $sort = 'hits';
    } else {
        array_push($fields, 'pubdate');
        $sort = 'date';
    }

    if (!empty($vars['showsummary'])) {
        array_push($fields, 'summary');
    }

    if (!empty($vars['showdynamic']) && xarModIsHooked('dynamicdata', 'articles')) {
        array_push($fields, 'dynamicdata');
    }

    $articles = xarModAPIFunc(
        'articles','user','getall',
        array(
            'ptid' => $ptid,
            'cids' => $cidsarray,
            'andcids' => 'false',
            'status' => $statusarray,
            'enddate' => time(),
            'fields' => $fields,
            'sort' => $sort,
            'numitems' => $vars['numitems']
        )
    );

    if (!isset($articles) || !is_array($articles) || count($articles) == 0) {
       return;
    }

    $items = array();
    foreach ($articles as $article) {
        $article['title'] = xarVarPrepHTMLDisplay($article['title']);
        if ($article['aid'] != $curaid) {
            $article['link'] = xarModURL(
                'articles', 'user', 'display',
                array(
                    'aid' => $article['aid'],
                    'ptid' => $article['pubtypeid']
                )
            );
        } else {
            $article['link'] = '';
        }

        if (!empty($vars['showvalue'])) {
            if ($vars['toptype'] == 'rating') {
                $article['value'] = intval($article['rating']);
            } elseif ($vars['toptype'] == 'hits') {
                $article['value'] = $article['counter'];
            } else {
                // TODO: make user-dependent
                if (!empty($article['pubdate'])) {
                    $article['value'] = strftime("%Y-%m-%d", $article['pubdate']);
                } else {
                    $article['value'] = 0;
                }
            }
        } else {
            $article['value'] = 0;
        }

        // MikeC: Bring the summary field back as $desc
        if (!empty($vars['showsummary'])) {
            $article['summary']  = xarVarPrepHTMLDisplay($article['summary']);
        } else {
            $article['summary'] = '';
        }

        // MarieA: Bring the pubtype description back as $descr
		if (!empty($vars['nopublimit'])) {
			$article['pubtypedescr'] = $pubtypes[$article['pubtypeid']]['descr'];
		}
        // this will also pass any dynamic data fields (if any)
        $items[] = $article;
    }

    // Populate block info and pass to theme
    if (count($items) > 0) {
        $blockinfo['content'] = array('items' => $items);
        return $blockinfo;
    }
}

/**
 * built-in block help/information system.
 */
function articles_topitemsblock_help()
{
    return '';
}

?>