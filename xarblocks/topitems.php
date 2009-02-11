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
 * initialise block
 * @author Jim McDonald
 */
function publications_topitemsblock_init()
{
    // Initial values when the block is created.
    return array(
        'numitems' => 5,
        'pubtype_id' => 0,
        'nopublimit' => false,
        'linkpubtype' => true,
        'catfilter' => 0,
        'includechildren' => false,
        'nocatlimit' => true,
        'linkcat' => false,
        'dynamictitle' => true,
        'toptype' => 'hits',
        'showvalue' => true,
        'showsummary' => false,
        'showdynamic' => false,
        'status' => '2,3',
        'nocache' => 0, // cache by default
        'pageshared' => 1, // share across pages (change if you use dynamic pubtypes et al.)
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
}

/**
 * get information on block
 */
function publications_topitemsblock_info()
{
    // Values
    return array(
        'text_type' => 'Top Items',
        'module' => 'publications',
        'text_type_long' => 'Show top publications',
        'allow_multiple' => true,
        'form_content' => false, // Deprecated.
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 * @author Jim McDonald
 */
function publications_topitemsblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadPublicationsBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block
    if (is_string($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // This is to maintain legacy consistancy
    if (!isset($vars['linkpubtype'])) {
        $vars['linkpubtype'] = true;
    }

    // see if we're currently displaying an article
    if (xarVarIsCached('Blocks.publications', 'id')) {
        $curid = xarVarGetCached('Blocks.publications', 'id');
    } else {
        $curid = -1;
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
            if (xarVarIsCached('Blocks.publications', 'cids')) {
                $curcids = xarVarGetCached('Blocks.publications', 'cids');
                if (!empty($curcids)) {
                    if ($curid == -1) {
                        //$cid = $curcids[0]['name'];
                        $cid = $curcids[0];
                        $cidsarray = array($curcids[0]);
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

        //echo $includechildren;
        if (!empty($vars['includechildren']) && !empty($cidsarray[0]) && !strstr($cidsarray[0],'_')) {
            $cidsarray[0] = '_' . $cidsarray[0];
        }

        if (!empty($cid)) {
            // if we're viewing all items below a certain category, i.e. catid = _NN
            $cid = str_replace('_', '', $cid);
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
    $pubtypes = xarModAPIFunc('publications', 'user', 'getpubtypes');

    if (!empty($vars['nopublimit'])) {
        //don't limit by pubtype
        $ptid = 0;
        if (!empty($vars['dynamictitle'])) {
            $blockinfo['title'] .= ' ' . xarML('Content');
        }
    } else {
        // MikeC: Check to see if admin has specified that only a specific
        // Publication Type should be displayed.  If not, then default to original TopItems configuration.
        if ($vars['pubtype_id'] == 0)
        {
            if (xarVarIsCached('Blocks.publications', 'ptid')) {
                $ptid = xarVarGetCached('Blocks.publications', 'ptid');
            }
            if (empty($ptid)) {
                // default publication type
                $ptid = xarModVars::get('publications', 'defaultpubtype');
            }
        } else {
            // MikeC: Admin Specified a publication type, use it.
            $ptid = $vars['pubtype_id'];
        }

        if (!empty($vars['dynamictitle'])) {
            if (!empty($ptid) && isset($pubtypes[$ptid]['description'])) {
                $blockinfo['title'] .= ' ' . xarVarPrepForDisplay($pubtypes[$ptid]['description']);
            } else {
                $blockinfo['title'] .= ' ' . xarML('Content');
            }
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
    $fields = array('id', 'title', 'pubtype_id', 'cids');
    if ($vars['toptype'] == 'rating' && xarModIsHooked('ratings', 'publications', $ptid)) {
        array_push($fields, 'rating');
        $sort = 'rating';
    } elseif ($vars['toptype'] == 'hits' && xarModIsHooked('hitcount', 'publications', $ptid)) {
        array_push($fields, 'counter');
        $sort = 'hits';
    } else {
        array_push($fields, 'pubdate');
        $sort = 'date';
    }

    if (!empty($vars['showsummary'])) {
        array_push($fields, 'summary');
    }
    if (!empty($vars['showdynamic']) && xarModIsHooked('dynamicdata', 'publications', $ptid)) {
        array_push($fields, 'dynamicdata');
    }

    $publications = xarModAPIFunc(
        'publications','user','getall',
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

    if (!isset($publications) || !is_array($publications) || count($publications) == 0) {
       return;
    }

    $items = array();
    foreach ($publications as $article) {
        $article['title'] = xarVarPrepHTMLDisplay($article['title']);
        if ($article['id'] != $curid) {
            // Use the filtered category if set, and not including children
            $article['link'] = xarModURL(
                'publications', 'user', 'display',
                array(
                    'id' => $article['id'],
                    'ptid' => (!empty($vars['linkpubtype']) ? $article['pubtype_id'] : NULL),
                    'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                )
            );
        } else {
            $article['link'] = '';
        }

        if (!empty($vars['showvalue'])) {
            if ($vars['toptype'] == 'rating') {
                if (!empty($article['rating'])) {
                  $article['value'] = intval($article['rating']);
                }else {
                    $article['value']=0;
                }
            } elseif ($vars['toptype'] == 'hits') {
                if (!empty($article['counter'])) {
                    $article['value'] = $article['counter'];
                } else {
                    $article['value'] = 0;
                }
            } else {
                // TODO: make user-dependent
                if (!empty($article['pubdate'])) {
                    //$article['value'] = strftime("%Y-%m-%d", $article['pubdate']);
                      $article['value'] = xarLocaleGetFormattedDate('short',$article['pubdate']);
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
            $article['transform'] = array('summary', 'title');
            $article = xarModCallHooks('item', 'transform', $article['id'], $article, 'publications');
        } else {
            $article['summary'] = '';
        }

        // MarieA: Bring the pubtype description back as $descr
        if (!empty($vars['nopublimit'])) {
            $article['pubtypedescr'] = $pubtypes[$article['pubtype_id']]['description'];
            //jojodee: while we are here bring the pubtype name back as well
            $article['pubtypename'] = $pubtypes[$article['pubtype_id']]['name'];
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
function publications_topitemsblock_help()
{
    return '';
}

?>