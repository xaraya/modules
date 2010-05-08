<?php
/**
 * Top Items Block
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 *
 */
/**
 * initialise block
 * @author Jim McDonald
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Articles_TopitemsBlock extends BasicBlock implements iBlock
{
    public $name                = 'TopitemsBlock';
    public $module              = 'articles';
    public $text_type           = 'Top Items';
    public $text_type_long      = 'Show top articles';
    public $pageshared          = 1;
    public $usershared          = 1;
    public $nocache             = 0;

    public $numitems = 5;
    public $pubtypeid = 0;
    public $nopublimit = false;
    public $linkpubtype = true;
    public $catfilter = 0;
    public $includechildren = false;
    public $nocatlimit = true;
    public $linkcat = false;
    public $dynamictitle = true;
    public $toptype = 'hits';
    public $showvalue = true;
    public $showsummary = false;
    public $showdynamic = false;
    public $status = '2,3';

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $args=array())
    {
        $data = parent::display($args);
        if (empty($data)) return;

        $vars = $data['content'];
        // This is to maintain legacy consistancy
        if (!isset($vars['linkpubtype'])) {
            $vars['linkpubtype'] = true;
        }

        // see if we're currently displaying an article
        if (xarVarIsCached('Blocks.articles', 'aid')) {
            $curaid = xarVarGetCached('Blocks.articles', 'aid');
        } else {
            $curaid = -1;
        }

        if (!empty($vars['dynamictitle'])) {
            if ($vars['toptype'] == 'rating') {
                $data['title'] = xarML('Top Rated');
            } elseif ($vars['toptype'] == 'hits') {
                $data['title'] = xarML('Top');
            } else {
                $data['title'] = xarML('Latest');
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
                $thiscategory = xarMod::apiFunc(
                    'categories','user','getcat',
                    array('cid' => $cid, 'return_itself' => 'return_itself')
                );
            }
            if ((!empty($cidsarray)) && (isset($thiscategory[0]['name'])) && !empty($vars['dynamictitle'])) {
                $data['title'] .= ' ' . $thiscategory[0]['name'];
            }
        }

        // Get publication types
        // MarieA - moved to always get pubtypes.
        $pubtypes = xarMod::apiFunc('articles', 'user', 'getpubtypes');

        if (!empty($vars['nopublimit'])) {
            //don't limit by pubtype
            $ptid = 0;
            if (!empty($vars['dynamictitle'])) {
                $data['title'] .= ' ' . xarML('Content');
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
                    $ptid = xarModVars::get('articles', 'defaultpubtype');
                }
            } else {
                // MikeC: Admin Specified a publication type, use it.
                $ptid = $vars['pubtypeid'];
            }

            if (!empty($vars['dynamictitle'])) {
                if (!empty($ptid) && isset($pubtypes[$ptid]['descr'])) {
                    $data['title'] .= ' ' . xarVarPrepForDisplay($pubtypes[$ptid]['descr']);
                } else {
                    $data['title'] .= ' ' . xarML('Content');
                }
            }
        }

        // frontpage or approved status
        if (empty($vars['status'])) {
            $statusarray = array(2,3);
        } elseif (!is_array($vars['status'])) {
            $statusarray = explode(',', $vars['status']);
        } else {
            $statusarray = $vars['status'];
        }

        // get cids for security check in getall
        $fields = array('aid', 'title', 'pubtypeid', 'cids');
        if ($vars['toptype'] == 'rating' && xarModIsHooked('ratings', 'articles', $ptid)) {
            array_push($fields, 'rating');
            $sort = 'rating';
        } elseif ($vars['toptype'] == 'hits' && xarModIsHooked('hitcount', 'articles', $ptid)) {
            array_push($fields, 'counter');
            $sort = 'hits';
        } else {
            array_push($fields, 'pubdate');
            $sort = 'date';
        }

        if (!empty($vars['showsummary'])) {
            array_push($fields, 'summary');
        }
        if (!empty($vars['showdynamic']) && xarModIsHooked('dynamicdata', 'articles', $ptid)) {
            array_push($fields, 'dynamicdata');
        }

        $articles = xarMod::apiFunc(
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
                // Use the filtered category if set, and not including children
                $article['link'] = xarModURL(
                    'articles', 'user', 'display',
                    array(
                        'aid' => $article['aid'],
                        'ptid' => (!empty($vars['linkpubtype']) ? $article['pubtypeid'] : NULL),
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
                $article = xarModCallHooks('item', 'transform', $article['aid'], $article, 'articles');
            } else {
                $article['summary'] = '';
            }

            // MarieA: Bring the pubtype description back as $descr
            if (!empty($vars['nopublimit'])) {
                $article['pubtypedescr'] = $pubtypes[$article['pubtypeid']]['descr'];
                //jojodee: while we are here bring the pubtype name back as well
                $article['pubtypename'] = $pubtypes[$article['pubtypeid']]['name'];
            }
            // this will also pass any dynamic data fields (if any)
            $items[] = $article;
        }

        // Populate block info and pass to theme
        if (count($items) > 0) {
            $vars['items'] = $items;
            $data['content'] = $vars;
            return $data;
        }
        return;
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        if (!isset($data['linkpubtype'])) {
            $data['linkpubtype'] = true;
        }
        if (!isset($data['includechildren'])) {
            $data['includechildren'] = false;
        }
        if (!isset($data['linkcat'])) {
            $data['linkcat'] = false;
        }

        $data['pubtypes'] = xarMod::apiFunc('articles', 'user', 'getpubtypes');
        $data['categorylist'] = xarMod::apiFunc('categories', 'user', 'getcat');

        $data['sortoptions'] = array(
            array('id' => 'hits', 'name' => xarML('Hit Count')),
            array('id' => 'rating', 'name' => xarML('Rating')),
            array('id' => 'date', 'name' => xarML('Date'))
        );

        $data['statusoptions'] = array(
            array('id' => '2,3', 'name' => xarML('All Published')),
            array('id' => '3', 'name' => xarML('Frontpage')),
            array('id' => '2', 'name' => xarML('Approved'))
        );

        // Return output
        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        $vars = array();
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

        $data['content'] = $vars;
        return $data;
    }

}
?>