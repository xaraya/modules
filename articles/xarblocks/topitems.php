<?php
// File: $Id: s.topitems.php 1.11 03/01/14 22:12:32+00:00 mikespub@sasquatch.pulpcontent.com $
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
    return true;
}

/**
 * get information on block
 */
function articles_topitemsblock_info()
{
    // Values
    return array('text_type' => 'Top Items',
                 'module' => 'articles',
                 'text_type_long' => 'Show top articles',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true);
}

/**
 * display block
 */
function articles_topitemsblock_display($blockinfo)
{
    // Security check
    if(!xarSecurityCheck('ReadArticlesBlock',1,'Block',$blockinfo['title'])) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    if (empty($vars['pubtypeid'])) {
        $vars['pubtypeid'] = 0;
    }
    if (empty($vars['nopublimit'])) {
        $vars['nopublimit'] = 0;
    }
    if (empty($vars['catfilter'])) {
        $vars['catfilter'] = '';
    }
    if (!isset($vars['nocatlimit'])) {
        $vars['nocatlimit'] = 1;
    }
    if (!isset($vars['dynamictitle'])) {
        $vars['dynamictitle'] = 1;
    }
    if (empty($vars['toptype'])) {
        $vars['toptype'] = 'hits';
    }
    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'hits') {
            $vars['showvalue'] = 1;
        } else {
            $vars['showvalue'] = 0;
        }
    }
    if (empty($vars['showsummary'])) {
        $vars['showsummary'] = 0;
    }
    if (empty($vars['showdynamic'])) {
        $vars['showdynamic'] = 0;
    }
    
    // see if we're currently displaying an article
    if (xarVarIsCached('Blocks.articles','aid')) {
        $curaid = xarVarGetCached('Blocks.articles','aid');
    } else {
        $curaid = -1;
    }
    
    //if (empty($vars['nopublimit']) || empty($vars['nocatlimit']) && ($vars['dynamictitle'] == 1)) {
    if ($vars['dynamictitle'] == 1) {
        if ($vars['toptype'] == 'rating') {
                $blockinfo['title'] = xarML('Top Rated');
            } elseif ($vars['toptype'] == 'hits') {
                $blockinfo['title'] = xarML('Top');
            } else {
                $blockinfo['title'] = xarML('Latest');
            }
        }

    if ($vars['nocatlimit'] == 1) {
        // don't limit by category
        $cid = 0;
        $cidsarray = array();
    } else {
        if(!empty($vars['catfilter'])) {
            // use admin defined category 
            $cidsarray = array($vars['catfilter']);
            $cid = $vars['catfilter'];
        } else {
            // use the current category
            // Jonn: this currently only works with one category at a time
            // it could be reworked to support multiple cids
            if(xarVarIsCached('Blocks.articles','cids')) {
                $curcids = xarVarGetCached('Blocks.articles','cids');
                if(!empty($curcids)) {
                    if($curaid == -1) {
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
        if(!empty($cid)) {
            // if we're viewing all items below a certain category, i.e. catid = _NN
            $cid = preg_replace('/_/','',$cid);
            $thiscategory = xarModAPIFunc('categories','user','getcat',
                                          array('cid' => $cid,
                                                'return_itself' => 'return_itself'
                                               )
                                         );
        }
        if ((!empty($cidsarray)) && (isset($thiscategory[0]['name'])) && ($vars['dynamictitle'] == 1)) {
            $blockinfo['title'] .= ' ' . $thiscategory[0]['name'];
        }
    }

	// Get publication types
	$pubtypes = xarModAPIFunc('articles','user','getpubtypes');  //MarieA - moved to always get pubtypes.

    if ($vars['nopublimit'] == 1) {
        //don't limit by pubtype
        $ptid = 0;
        if(empty($vars['nocatlimit']) || empty($vars['nopublimit']) || ($vars['dynamictitle'] == 1)) {
            $blockinfo['title'] .= ' ' . xarML('Content');
        }
    } else {
        // MikeC: Check to see if admin has specified that only a specific 
        // Publication Type should be displayed.  If not, then default to original TopItems configuration.
        if( $vars['pubtypeid'] == 0 )
        {
            if (xarVarIsCached('Blocks.articles','ptid')) {
                $ptid = xarVarGetCached('Blocks.articles','ptid');
            }
            if (empty($ptid)) {
                // default publication type
                $ptid = xarModGetVar('articles','defaultpubtype');
            }
        } else {
            // MikeC: Admin Specified a publication type, use it.
            $ptid = $vars['pubtypeid'];
        }
        
        if (!empty($ptid) && isset($pubtypes[$ptid]['descr']) && ($vars['dynamictitle'] == 1)) {
            $blockinfo['title'] .= ' ' . xarVarPrepForDisplay($pubtypes[$ptid]['descr']);
        }
    }

    // frontpage or approved status
    $status = array(3,2);

    // get cids for security check in getall
    $fields = array('aid','title','pubtypeid','cids');
    if ($vars['toptype'] == 'rating') {
        array_push($fields,'rating');
        $sort = 'rating';
    } elseif ($vars['toptype'] == 'hits') {
        array_push($fields,'counter');
        $sort = 'hits';
    } else {
        array_push($fields,'pubdate');
        $sort = 'date';
    }
    // MikeC: Added the 'summary' field to the field list
    if ($vars['showsummary']) {
        array_push($fields,'summary');
    }
    if ($vars['showdynamic'] && xarModIsHooked('dynamicdata','articles')) {
        array_push($fields,'dynamicdata');
    }

    $articles = xarModAPIFunc('articles','user','getall',
                              array('ptid' => $ptid,
                                    'cids' => $cidsarray,
                                    'andcids' => 'false',
                                    'status' => $status,
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
            $article['link'] = xarModURL('articles',
                                         'user',
                                         'display',
                                         array('aid' => $article['aid'],
                                               'ptid' => $article['pubtypeid']));
        } else {
            $article['link'] = '';
        }
        if ($vars['showvalue']) {
            if ($vars['toptype'] == 'rating') {
                $article['value'] = intval($article['rating']);
            } elseif ($vars['toptype'] == 'hits') {
                $article['value'] = $article['counter'];
            } else {
            // TODO: make user-dependent
                if (!empty($article['pubdate'])) {
                    $article['value'] = strftime("%Y-%m-%d",$article['pubdate']);
                } else {
                    $article['value'] = 0;
                }
            }
        } else {
            $article['value'] = 0;
        }
        // MikeC: Bring the summary field back as $desc
        if ($vars['showsummary']) {
            $article['summary']  = xarVarPrepHTMLDisplay($article['summary']);
        } else {
            $article['summary'] = '';
        }
		//MarieA: Bring the pubtype description back as $descr
		if ($vars['nopublimit'] == 1) {
			$article['pubtypedescr'] = $pubtypes[$article['pubtypeid']]['descr'];
		}
        // this will also pass any dynamic data fields (if any)
        $items[] = $article;
    }

    // Populate block info and pass to theme
    if (count($items) > 0) {
        if (empty($blockinfo['template'])) {
            $template = 'topitems';
        } else {
            $template = $blockinfo['template'];
        }
        $blockinfo['content'] = xarTplBlock('articles',$template,
                                            array('items' => $items));

        return $blockinfo;
    }
}


/**
 * modify block settings
 */
function articles_topitemsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // MikeC: Added pubtype, for publication type, default to news.
    if (empty($vars['pubtypeid'])) {
        $vars['pubtypeid'] = 0;
    }
    if (empty($vars['nopublimit'])) {
        $vars['nopublimit'] = 0;
    }
    if (empty($vars['catfilter'])) {
        $vars['catfilter'] = '';
    }
    if (!isset($vars['nocatlimit'])) {
        $vars['nocatlimit'] = 1;
    }
    if (!isset($vars['dynamictitle'])) {
        $vars['dynamictitle'] = 1;
    }
    if (empty($vars['toptype'])) {
        $vars['toptype'] = 'hits';
    }
    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'rating') {
            $vars['showvalue'] = 0;
        } else {
            $vars['showvalue'] = 1;
        }
    }
    if (empty($vars['showsummary'])) {
        $vars['showsummary'] = 0;
    }
    if (empty($vars['showdynamic'])) {
        $vars['showdynamic'] = 0;
    }

    $vars['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    
    $vars['categorylist'] = xarModAPIFunc('categories','user','getcat');

    $vars['sortoptions'] = array(array('id' => 'hits',
                                       'name' => xarML('Hit Count')),
                                 array('id' => 'rating',
                                       'name' => xarML('Rating')),
                                 array('id' => 'date',
                                       'name' => xarML('Date')));

    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return xarTplBlock('articles','topitemsAdmin',$vars);
}

/**
 * update block settings
 */
function articles_topitemsblock_update($blockinfo)
{
	//MikeC: Make sure we retrieve the new pubtype from the configuration form.
    if(!xarVarFetch('numitems',     'isset', $vars['numitems'],     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('pubtypeid',    'isset', $vars['pubtypeid'],    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('nopublimit',   'isset', $vars['nopublimit'],   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catfilter',    'isset', $vars['catfilter'],    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('nocatlimit',   'isset', $vars['nocatlimit'],   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('dynamictitle', 'isset', $vars['dynamictitle'], NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('toptype',      'isset', $vars['toptype'],      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showsummary',  'isset', $vars['showsummary'],  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showdynamic',  'isset', $vars['showdynamic'],  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showvalue',    'isset', $vars['showvalue'],    NULL, XARVAR_DONT_SET)) {return;}

    if (empty($vars['nocatlimit'])) {
        $vars['nocatlimit'] = 0;
    }
    if (empty($vars['dynamictitle'])) {
        $vars['dynamictitle'] = 0;
    }
    if (empty($vars['showvalue'])) {
        $vars['showvalue'] = 0;
    }
    if (empty($vars['showsummary'])) {
        $vars['showsummary'] = 0;
    }
    if (empty($vars['showdynamic'])) {
        $vars['showdynamic'] = 0;
    }
    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function articles_topitemsblock_help()
{
    return '';
}

?>
