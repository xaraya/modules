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
    sys::import('xaraya.structures.containers.blocks.basicblock');

    class TopitemsBlock extends BasicBlock
    {
        public $numitems           = 5;
        public $pubtype_id         = 0;
        public $nopublimit         = false;
        public $linkpubtype        = true;
        public $catfilter          = 0;
        public $includechildren    = false;
        public $nocatlimit         = true;
        public $linkcat            = false;
        public $dynamictitle       = true;
        public $toptype            = 'hits';
        public $showvalue          = true;
        public $showsummary        = false;
        public $showdynamic        = false;
        public $state             = '2,3';

        public function __construct(ObjectDescriptor $descriptor)
        {
            parent::__costruct($descriptor);
            $this->text_type = 'Top Items';
            $this->text_type_long = 'Show top publications';
            $this->module = 'publications';
            $this->allow_multiple = true;
            $this->show_preview = true;
        }
        
        public function display(Array $data=array())
        {
            $data = parent::display($data);

            // see if we're currently displaying an article
            if (xarVarIsCached('Blocks.publications', 'id')) {
                $curid = xarVarGetCached('Blocks.publications', 'id');
            } else {
                $curid = -1;
            }

            if (!empty($data['dynamictitle'])) {
                if ($data['toptype'] == 'rating') {
                    $blockinfo['title'] = xarML('Top Rated');
                } elseif ($data['toptype'] == 'hits') {
                    $blockinfo['title'] = xarML('Top');
                } else {
                    $blockinfo['title'] = xarML('Latest');
                }
            }

            if (!empty($data['nocatlimit'])) {
                // don't limit by category
                $cid = 0;
                $cidsarray = array();
            } else {
                if (!empty($data['catfilter'])) {
                    // use admin defined category
                    $cidsarray = array($data['catfilter']);
                    $cid = $data['catfilter'];
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
                if (!empty($data['includechildren']) && !empty($cidsarray[0]) && !strstr($cidsarray[0],'_')) {
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
                if ((!empty($cidsarray)) && (isset($thiscategory[0]['name'])) && !empty($data['dynamictitle'])) {
                    $blockinfo['title'] .= ' ' . $thiscategory[0]['name'];
                }
            }

            // Get publication types
            // MarieA - moved to always get pubtypes.
            $publication_types = xarModAPIFunc('publications', 'user', 'get_pubtypes');

            if (!empty($data['nopublimit'])) {
                //don't limit by publication type
                $ptid = 0;
                if (!empty($data['dynamictitle'])) {
                    $blockinfo['title'] .= ' ' . xarML('Content');
                }
            } else {
                // MikeC: Check to see if admin has specified that only a specific
                // Publication Type should be displayed.  If not, then default to original TopItems configuration.
                if ($data['pubtype_id'] == 0)
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
                    $ptid = $data['pubtype_id'];
                }

                if (!empty($data['dynamictitle'])) {
                    if (!empty($ptid) && isset($publication_types[$ptid]['description'])) {
                        $blockinfo['title'] .= ' ' . xarVarPrepForDisplay($publication_types[$ptid]['description']);
                    } else {
                        $blockinfo['title'] .= ' ' . xarML('Content');
                    }
                }
            }

            // frontpage or approved state
            if (empty($data['state'])) {
                $statearray = array(2,3);
            } elseif (!is_array($data['state'])) {
                $statearray = split(',', $data['state']);
            } else {
                $statearray = $data['state'];
            }

            // get cids for security check in getall
            $fields = array('id', 'title', 'pubtype_id', 'cids');
            if ($data['toptype'] == 'rating' && xarModIsHooked('ratings', 'publications', $ptid)) {
                array_push($fields, 'rating');
                $sort = 'rating';
            } elseif ($data['toptype'] == 'hits' && xarModIsHooked('hitcount', 'publications', $ptid)) {
                array_push($fields, 'counter');
                $sort = 'hits';
            } else {
                array_push($fields, 'pubdate');
                $sort = 'date';
            }

            if (!empty($data['showsummary'])) {
                array_push($fields, 'summary');
            }
            if (!empty($data['showdynamic']) && xarModIsHooked('dynamicdata', 'publications', $ptid)) {
                array_push($fields, 'dynamicdata');
            }

            $publications = xarModAPIFunc(
                'publications','user','getall',
                array(
                    'ptid' => $ptid,
                    'cids' => $cidsarray,
                    'andcids' => 'false',
                    'state' => $statearray,
                    'enddate' => time(),
                    'fields' => $fields,
                    'sort' => $sort,
                    'numitems' => $data['numitems']
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
                            'ptid' => (!empty($data['linkpubtype']) ? $article['pubtype_id'] : NULL),
                            'catid' => ((!empty($data['linkcat']) && !empty($data['catfilter'])) ? $data['catfilter'] : NULL)
                        )
                    );
                } else {
                    $article['link'] = '';
                }

                if (!empty($data['showvalue'])) {
                    if ($data['toptype'] == 'rating') {
                        if (!empty($article['rating'])) {
                          $article['value'] = intval($article['rating']);
                        }else {
                            $article['value']=0;
                        }
                    } elseif ($data['toptype'] == 'hits') {
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
                if (!empty($data['showsummary'])) {
                    $article['summary']  = xarVarPrepHTMLDisplay($article['summary']);
                    $article['transform'] = array('summary', 'title');
                    $article = xarModCallHooks('item', 'transform', $article['id'], $article, 'publications');
                } else {
                    $article['summary'] = '';
                }

                // MarieA: Bring the pubtype description back as $descr
                if (!empty($data['nopublimit'])) {
                    $article['pubtypedescr'] = $publication_types[$article['pubtype_id']]['description'];
                    //jojodee: while we are here bring the pubtype name back as well
                    $article['pubtypename'] = $publication_types[$article['pubtype_id']]['name'];
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

        public function modify(Array $data=array())
        {
            $data = parent::modify($data);
            if (!isset($data['linkpubtype'])) $data['linkpubtype'] = $this->linkpubtype;
            if (!isset($data['includechildren'])) $data['includechildren'] = $this->includechildren;
            if (!isset($data['linkcat'])) $data['linkcat'] = $this->linkcat;
            return $data;
        }

        public function update(Array $data=array())
        {
            $data = parent::update($data);
            if (!xarVarFetch('numitems',        'int:1:200', $data['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('pubtype_id',      'id',        $data['pubtype_id'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('linkpubtype',     'checkbox',  $data['linkpubtype'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('nopublimit',      'checkbox',  $data['nopublimit'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('catfilter',       'id',        $data['catfilter'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('includechildren', 'checkbox',  $data['includechildren'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('nocatlimit',      'checkbox',  $data['nocatlimit'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('linkcat',         'checkbox',  $data['linkcat'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('dynamictitle',    'checkbox',  $data['dynamictitle'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('showsummary',     'checkbox',  $data['showsummary'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('showdynamic',     'checkbox',  $data['showdynamic'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('showvalue',       'checkbox',  $data['showvalue'], 0, XARVAR_NOT_REQUIRED)) {return;}
            if (!xarVarFetch('state',           'strlist:,:int:1:4', $data['state'])) {return;}
            if (!xarVarFetch('toptype',         'enum:hits:rating:date', $data['toptype'])) {return;}

            if ($data['nopublimit'] == true) {
                $data['pubtype_id'] = 0;
            }
            if ($data['nocatlimit']) {
                $data['catfilter'] = 1;
                $data['includechildren'] = 0;
            }
            if ($data['includechildren']) {
                $data['linkcat'] = 0;
            }

            return $data;
        }
    }
?>