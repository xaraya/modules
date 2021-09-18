<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * initialise block
 * @author Jim McDonald
 */
    sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_TopitemsBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'topitems';
    protected $module           = 'publications'; // module block type belongs to, if any
    protected $text_type        = 'Top Items';  // Block type display name
    protected $text_type_long   = 'Show top publications'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

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
    // chris: state is a reserved property name used by blocks
    //public $state               = '2,3';
    public $pubstate            = '2,3';


    public function display(array $data=[])
    {
        $data = $this->getContent();

        // see if we're currently displaying an article
        if (xarVar::isCached('Blocks.publications', 'id')) {
            $curid = xarCoreCache::getCached('Blocks.publications', 'id');
        } else {
            $curid = -1;
        }

        if (!empty($data['dynamictitle'])) {
            if ($data['toptype'] == 'rating') {
                $data['title'] = xarML('Top Rated');
            } elseif ($data['toptype'] == 'hits') {
                $data['title'] = xarML('Top');
            } else {
                $data['title'] = xarML('Latest');
            }
        }

        if (!empty($data['nocatlimit'])) {
            // don't limit by category
            $cid = 0;
            $cidsarray = [];
        } else {
            if (!empty($data['catfilter'])) {
                // use admin defined category
                $cidsarray = [$data['catfilter']];
                $cid = $data['catfilter'];
            } else {
                // use the current category
                // Jonn: this currently only works with one category at a time
                // it could be reworked to support multiple cids
                if (xarVar::isCached('Blocks.publications', 'cids')) {
                    $curcids = xarCoreCache::getCached('Blocks.publications', 'cids');
                    if (!empty($curcids)) {
                        if ($curid == -1) {
                            //$cid = $curcids[0]['name'];
                            $cid = $curcids[0];
                            $cidsarray = [$curcids[0]];
                        } else {
                            $cid = $curcids[0];
                            $cidsarray = [$curcids[0]];
                        }
                    } else {
                        $cid = 0;
                        $cidsarray = [];
                    }
                } else {
                    // pull from all categories
                    $cid = 0;
                    $cidsarray = [];
                }
            }

            //echo $includechildren;
            if (!empty($data['includechildren']) && !empty($cidsarray[0]) && !strstr($cidsarray[0], '_')) {
                $cidsarray[0] = '_' . $cidsarray[0];
            }

            if (!empty($cid)) {
                // if we're viewing all items below a certain category, i.e. catid = _NN
                $cid = str_replace('_', '', $cid);
                $thiscategory = xarMod::apiFunc(
                    'categories',
                    'user',
                    'getcat',
                    ['cid' => $cid, 'return_itself' => 'return_itself']
                );
            }
            if ((!empty($cidsarray)) && (isset($thiscategory[0]['name'])) && !empty($data['dynamictitle'])) {
                $data['title'] .= ' ' . $thiscategory[0]['name'];
            }
        }

        // Get publication types
        // MarieA - moved to always get pubtypes.
        $publication_types = xarMod::apiFunc('publications', 'user', 'get_pubtypes');

        if (!empty($data['nopublimit'])) {
            //don't limit by publication type
            $ptid = 0;
            if (!empty($data['dynamictitle'])) {
                $data['title'] .= ' ' . xarML('Content');
            }
        } else {
            // MikeC: Check to see if admin has specified that only a specific
            // Publication Type should be displayed.  If not, then default to original TopItems configuration.
            if ($data['pubtype_id'] == 0) {
                if (xarVar::isCached('Blocks.publications', 'ptid')) {
                    $ptid = xarCoreCache::getCached('Blocks.publications', 'ptid');
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
                    $data['title'] .= ' ' . xarVar::prepForDisplay($publication_types[$ptid]['description']);
                } else {
                    $data['title'] .= ' ' . xarML('Content');
                }
            }
        }

        // frontpage or approved state
        if (empty($data['pubstate'])) {
            $statearray = [2,3];
        } elseif (!is_array($data['pubstate'])) {
            $statearray = preg_split('/,/', $data['pubstate']);
        } else {
            $statearray = $data['pubstate'];
        }

        // get cids for security check in getall
        $fields = ['id', 'title', 'pubtype_id', 'cids'];
        if ($data['toptype'] == 'rating' && xarModHooks::isHooked('ratings', 'publications', $ptid)) {
            array_push($fields, 'rating');
            $sort = 'rating';
        } elseif ($data['toptype'] == 'hits' && xarModHooks::isHooked('hitcount', 'publications', $ptid)) {
            array_push($fields, 'counter');
            $sort = 'hits';
        } else {
            array_push($fields, 'create_date');
            $sort = 'date';
        }

        if (!empty($data['showsummary'])) {
            array_push($fields, 'summary');
        }
        if (!empty($data['showdynamic']) && xarModHooks::isHooked('dynamicdata', 'publications', $ptid)) {
            array_push($fields, 'dynamicdata');
        }

        $publications = xarMod::apiFunc(
            'publications',
            'user',
            'getall',
            [
                    'ptid' => $ptid,
                    'cids' => $cidsarray,
                    'andcids' => 'false',
                    'state' => $statearray,
                    'create_date' => time(),
                    'fields' => $fields,
                    'sort' => $sort,
                    'numitems' => $data['numitems'],
                ]
        );

        if (!isset($publications) || !is_array($publications) || count($publications) == 0) {
            return;
        }
        $items = [];
        foreach ($publications as $article) {
            $article['title'] = xarVar::prepHTMLDisplay($article['title']);
            if ($article['id'] != $curid) {
                // Use the filtered category if set, and not including children
                $article['link'] = xarController::URL(
                    'publications',
                    'user',
                    'display',
                    [
                            'itemid' => $article['id'],
                            'catid' => ((!empty($data['linkcat']) && !empty($data['catfilter'])) ? $data['catfilter'] : null),
                        ]
                );
            } else {
                $article['link'] = '';
            }

            if (!empty($data['showvalue'])) {
                if ($data['toptype'] == 'rating') {
                    if (!empty($article['rating'])) {
                        $article['value'] = intval($article['rating']);
                    } else {
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
                    if (!empty($article['create_date'])) {
                        //$article['value'] = strftime("%Y-%m-%d", $article['create_date']);
                        $article['value'] = xarLocale::getFormattedDate('short', $article['create_date']);
                    } else {
                        $article['value'] = 0;
                    }
                }
            } else {
                $article['value'] = 0;
            }

            // MikeC: Bring the summary field back as $desc
            if (!empty($data['showsummary'])) {
                $article['summary']  = xarVar::prepHTMLDisplay($article['summary']);
                $article['transform'] = ['summary', 'title'];
                $article = xarModHooks::call('item', 'transform', $article['id'], $article, 'publications');
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
        $data['items'] = $items;
        if (!empty($data['dynamictitle'])) {
            $this->setTitle($data['title']);
        }
        return $data;
    }
}
