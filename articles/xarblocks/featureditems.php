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
    return true;
}

/**
 * get information on block
 */
function articles_featureditemsblock_info()
{
    // Values
    return array('text_type' => 'Featured Items',
                 'module' => 'articles',
                 'text_type_long' => 'Show featured articles',
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => false,
                 'show_preview' => true
                );
}

/**
 * display block
 */
function articles_featureditemsblock_display($blockinfo)
{
    // Security check
    if(!xarSecurityCheck('ReadArticlesBlock',1,'Block',$blockinfo['title'])) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['featuredaid'])) {
        $vars['featuredaid'] = 0;
    }
    if (empty($vars['alttitle'])) {
        $vars['alttitle'] = '';
    }
    if (empty($vars['showfeaturedsum'])) {
        $vars['showfeaturedsum'] = 0;
    }
    if (empty($vars['toptype'])) {
        $vars['toptype'] = 'hits';
    }
    if (empty($vars['linkpubtype'])) {
        $vars['linkpubtype'] = 0;
    }
    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'hits') {
            $vars['showvalue'] = 1;
        } else {
            $vars['showvalue'] = 0;
        }
    }
    if (empty($vars['moreitems'])) {
        $vars['moreitems'] = array();
    }
    if (empty($vars['showsummary'])) {
        $vars['showsummary'] = 0;
    }
/*    if (empty($vars['showdynamic'])) {
        $vars['showdynamic'] = 0;
    }*/

    // Load articles user API
    if (!xarModAPILoad('articles','user')) return;

    $featuredaid = $vars['featuredaid'];

    $alttitle = $vars['alttitle'];

    $fields = array('aid','title');
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
    if ($vars['showsummary'] == 1) {
        array_push($fields,'summary');
    }
    /*if ($vars['showdynamic'] && xarModIsHooked('dynamicdata','articles')) {
        array_push($fields,'dynamicdata');
    }*/

    if(($featuredaid)>0) {
        $featuredart = xarModAPIFunc(
            'articles','user','get',
            array(
                'aid' => $featuredaid,
                'fields' => $fields
            )
        );
        $featuredlabel = xarVarPrepForDisplay($featuredart['title']);

        $featuredlink = xarModURL(
            'articles', 'user', 'display',
            array(
                'aid' => $featuredart['aid'],
                'itemtype' => (!empty($vars['linkpubtype']) ? $featuredart['pubtypeid'] : NULL)
            )
        );

        if ($vars['showfeaturedsum'] == 1) {
            $featureddesc  = xarVarPrepHTMLDisplay($featuredart['summary']);
        } else {
            $featureddesc = '';
        }

        $data['feature'][] = array('featuredlabel' => $featuredlabel,
                                   'featuredlink' => $featuredlink,
                                   'alttitle' => $alttitle,
                                   'featureddesc' => $featureddesc
                                  );
    }
    if (!empty($vars['moreitems'])) {
        $articles = xarModAPIFunc(
            'articles','user','getall',
            array(
                'aids' => $vars['moreitems'],
                'enddate' => time(),
                'fields' => $fields,
                'sort' => $sort
            )
        );

        /*if (!isset($articles) || !is_array($articles) || count($articles) == 0) {
            return;
        }*/

        // see if we're currently displaying an article
        if (xarVarIsCached('Blocks.articles','aid')) {
            $curaid = xarVarGetCached('Blocks.articles','aid');
        } else {
            $curaid = -1;
        }

        $data['items'] = array ();
        foreach ($articles as $article) {
            $label = xarVarPrepForDisplay($article['title']);
            if ($article['aid'] != $curaid) {
                $link = xarModURL(
                    'articles', 'user', 'display',
                    array(
                        'aid' => $article['aid'],
                        'itemtype' => (!empty($vars['linkpubtype']) ? $article['pubtypeid'] : NULL)
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
                        $count = strftime("%Y-%m-%d",$article['pubdate']);
                    } else {
                        $count = 0;
                    }
                }
            } else {
                $count = 0;
            }

            // MikeC: Bring the summary field back as $desc
            if ($vars['showsummary'] == 1) {
                $desc  = xarVarPrepHTMLDisplay($article['summary']);
            } else {
                $desc = '';
            }

            // MikeC: Pass $desc to items[] array so that the block template can render it
            $data['items'][] = array('label' => $label,
                                                'link' => $link,
                                                'count' => $count,
                                                'desc' => $desc
                                    );
        }
    }

    // Populate block info and pass to theme
    if (!empty($vars['featuredaid'])) {
        if (empty($blockinfo['template'])) {
            $template = 'featureditems';
        } else {
            $template = $blockinfo['template'];
        }
        $blockinfo['content'] = xarTplBlock('articles', $template, $data);

        return $blockinfo;
    }
}


/**
 * modify block settings
 */
function articles_featureditemsblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['pubtypeid'])) {
        $vars['pubtypeid'] = '';
    }

    if (empty($vars['catfilter'])) {
        $vars['catfilter'] = '';
    }

    if (empty($vars['status'])) {
        $vars['status'] = array(3,2);
    }

    if (empty($vars['itemlimit'])) {
        $vars['itemlimit'] = 10;
    }

    if (empty($vars['featuredaid'])) {
        $vars['featuredaid'] = 0;
    }

    if (empty($vars['alttitle'])) {
        $vars['alttitle'] = '';
    }

    if (empty($vars['showfeaturedsum'])) {
        $vars['showfeaturedsum'] = 0;
    }

    if (empty($vars['moreitems'])) {
        $vars['moreitems'] = array();
    }

    if (empty($vars['toptype'])) {
        $vars['toptype'] = 'date';
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

    if (empty($vars['linkpubtype'])) {
        $vars['linkpubtype'] = 0;
    }

//    if (empty($vars['showdynamic'])) {
//        $vars['showdynamic'] = 0;
//    }

    $vars['fields'] = array('aid','title');

    if (!is_array($vars['status'])) {
        $statusarray = array($vars['status']);
    } else {
	    $statusarray = $vars['status'];
    }

    if(!empty($vars['catfilter'])) {
        $cidsarray = array($vars['catfilter']);
    } else {
        $cidsarray = array();
    }

    $vars['filtereditems'] = xarModAPIFunc('articles','user','getall',
                                           array('ptid' => $vars['pubtypeid'],
                                                 'cids' => $cidsarray,
                                                 'enddate' => time(),
                                                 'status' => $statusarray,
                                                 'fields' => $vars['fields'],
                                                 'sort' => $vars['toptype'],
                                                 'numitems' => $vars['itemlimit']
                                                )
                                          );

    $vars['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');

    $vars['categorylist'] = xarModAPIFunc('categories','user','getcat');

    $vars['statusoptions'] = array(array('id' => '',
                                         'name' => xarML('All Published')),
                                   array('id' => '3',
                                         'name' => xarML('Frontpage')),
                                   array('id' => '2',
                                         'name' => xarML('Approved'))
                                  );

    $vars['sortoptions'] = array(array('id' => 'hits',
                                       'name' => xarML('Hit Count')),
                                 array('id' => 'rating',
                                       'name' => xarML('Rating')),
                                 array('id' => 'date',
                                       'name' => xarML('Date'))
                                );

    //Put together the more featured articles list
    for($idx=0; $idx < count($vars['filtereditems']); ++$idx) {
        $vars['filtereditems'][$idx]['selected'] = '';
        for($mx=0; $mx < count($vars['moreitems']); ++$mx) {
            if (($vars['moreitems'][$mx]) == ($vars['filtereditems'][$idx]['aid'])) {
                $vars['filtereditems'][$idx]['selected'] = 'selected="selected"';
            }
        }
    }
    $vars['morearticles'] = $vars['filtereditems'];
    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return xarTplBlock('articles','featureditemsAdmin',$vars);
}

/**
 * update block settings
 */
function articles_featureditemsblock_update($blockinfo)
{
    //MikeC: Make sure we retrieve the new pubtype from the configuration form.
    list(
        $vars['pubtypeid'],
        $vars['catfilter'],
        $vars['status'],
        $vars['itemlimit'],
        $vars['toptype'],
        $vars['featuredaid'],
        $vars['alttitle'],
        $vars['showfeaturedsum'],
        $vars['moreitems'],
        $vars['showsummary'],
        //$vars['showdynamic'],
        $vars['showvalue'],
        $vars['linkpubtype']
    ) = xarVarCleanFromInput(
        'pubtypeid',
        'catfilter',
        'status',
        'itemlimit',
        'toptype',
        'featuredaid',
        'alttitle',
        'showfeaturedsum',
        'moreitems',
        'showsummary',
        //'showdynamic',
        'showvalue',
        'linkpubtype'
    );

    if (empty($vars['showfeaturedsum'])) {
        $vars['showfeaturedsum'] = 0;
    }
    if (empty($vars['showvalue'])) {
        $vars['showvalue'] = 0;
    }
    if (empty($vars['showsummary'])) {
        $vars['showsummary'] = 0;
    }
    if (empty($vars['linkpubtype'])) {
        $vars['linkpubtype'] = 0;
    }
//    if (empty($vars['showdynamic'])) {
//        $vars['showdynamic'] = 0;
//    }
    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function articles_featureditemsblock_help()
{
    $output = new pnHTML();

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text('Any featureditems block info should be placed in your modname_blocknameblock_help() function.');
    $output->LineBreak(2);
    $output->Text('More information.');
    $output->SetInputMode(_PNH_PARSEINPUT);

    return $output->GetOutput();
}

?>
