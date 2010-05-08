<?php
/**
 * Featured items
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
 * Featured initialise block
 *
 * @author Jonn Beams (based on code from TopItems block)
 *
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Articles_FeatureditemsBlock extends BasicBlock implements iBlock
{
    public $name                = 'FeatureditemsBlock';
    public $module              = 'articles';
    public $text_type           = 'Featured Items';
    public $text_type_long      = 'Show featured articles';
    public $pageshared          = 1;
    public $nocache             = 1;

    public $featuredaid = 0;
    public $alttitle    = '';
    public $altsummary  = '';
    public $moreitems   = array();
    public $toptype     = 'date';
    public $showvalue   = true;
    public $pubtypeid   = '';
    public $catfilter   = '';
    public $status      = array(3,2);
    public $itemlimit   = 10;
    public $showfeaturedsum = false;
    public $showfeaturedbod = false;
    public $showsummary     = false;
    public $linkpubtype     = false;
    public $linkcat         = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $args=array())
    {
        $data = parent::display($args);
        if (empty($data)) return;

        $vars = $data['content'];

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

        $fields = array('aid', 'title', 'cids');

        $fields[] = 'dynamicdata';

        // Initialize arrays
        $vars['feature'] = array();
        $vars['items'] = array();

        // Setup featured item
        if ($featuredaid > 0) {

            if (xarModIsHooked('uploads', 'articles', $vars['pubtypeid'])) {
                xarVarSetCached('Hooks.uploads','ishooked',1);
            }

            if($featart = xarMod::apiFunc(
                'articles','user','getall',
                array(
                    'aids' => array($featuredaid),
                    'extra' => array('cids','dynamicdata')))) {

                    foreach($featart as $featuredart) {

                $fieldlist = array('aid', 'title', 'summary', 'authorid', 'pubdate',
                                   'pubtypeid', 'notes', 'status', 'body', 'cids');

                $featuredlink = xarModURL(
                    'articles', 'user', 'display',
                    array(
                        'aid' => $featuredart['aid'],
                        'itemtype' => (!empty($vars['linkpubtype']) ? $featuredart['pubtypeid'] : NULL),
                        'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                    )
                );
                if (empty($vars['showfeaturedbod'])) {$vars['showfeaturedbod'] = false;}
                if(!isset($featuredart['cids'])) $featuredart['cids'] = "";

                $feature= array(
                    'featuredlabel'     => $featuredart['title'],
                    'featuredlink'      => $featuredlink,
                    'alttitle'          => $vars['alttitle'],
                    'altsummary'        => $vars['altsummary'],
                    'showfeaturedsum'   => $vars['showfeaturedsum'],
                    'showfeaturedbod'   => $vars['showfeaturedbod'],
                    'featureddesc'      => $featuredart['summary'],
                    'featuredbody'      => $featuredart['body'],
                    'featuredcids'      => $featuredart['cids'],
                    'pubtypeid'         => $featuredart['pubtypeid'],
                    'featuredaid'       => $featuredart['aid'],
                    'featureddate'      => $featuredart['pubdate']
                );

                // Get rid of the default fields so all we have left are the DD ones
                foreach ($fieldlist as $field) {
                    if (isset($featuredart[$field])) {
                        unset($featuredart[$field]);
                    }
                }

                // now add the DD fields to the featuredart
                $feature = array_merge($featuredart, $feature);
                $vars['feature'][] = $feature;
            }
        }

        // Setup additional items
        $fields = array('aid', 'title', 'pubtypeid', 'cids');

        // Added the 'summary' field to the field list.
        if (!empty($vars['showsummary'])) {
            $fields[] = 'summary';
        }

        if ($vars['toptype'] == 'rating') {
            $fields[] = 'rating';
            $sort = 'rating';
        } elseif ($vars['toptype'] == 'hits') {
            $fields[] = 'counter';
            $sort = 'hits';
        } elseif ($vars['toptype'] == 'date') {
            $fields[] = 'pubdate';
            $sort = 'date';
        } else {
           $sort = $vars['toptype'];
        }

        if (!empty($vars['moreitems'])) {
            $articles = xarMod::apiFunc(
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

                $count = '';
                // TODO: find a nice clean way to show all sort types
                if ($vars['showvalue']) {
                    if ($vars['toptype'] == 'rating') {
                        $count = intval($article['rating']);
                    } elseif ($vars['toptype'] == 'hits') {
                        $count = $article['counter'];
                    } elseif ($vars['toptype'] == 'date') {
                        // TODO: make user-dependent
                        if (!empty($article['pubdate'])) {
                            $count = strftime("%Y-%m-%d", $article['pubdate']);
                        } else {
                            $count = 0;
                        }
                    } else {
                        $count = 0;
                    }
                } else {
                    $count = 0;
                }
                if (isset($article['cids'])) {
                   $cids=$article['cids'];
                }else{
                   $cids='';
                }
                if (isset($article['pubdate'])) {
                   $pubdate=$article['pubdate'];
                }else{
                   $pubdate='';
                }
                // Pass $desc to items[] array so that the block template can render it
                $vars['items'][] = array(
                    'label' => $article['title'],
                    'link' => $link,
                    'count' => $count,
                    'cids' => $cids,
                    'pubdate' => $pubdate,
                    'desc' => ((!empty($vars['showsummary']) && !empty($article['summary'])) ? $article['summary'] : ''),
                    'aid' => $article['aid']
                );
            }
        }}
        if (empty($vars['feature']) && empty($vars['items'])) {
            // Nothing to display.
            return;
        }

        // Set the data to return.
        $vars['name'] = $this->name;
        $vars['bid'] = $this->bid;
        $vars['module'] = $this->module;
        $vars['type'] = $this->type;
        $data['content'] = $vars;
        return $data;
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);
        // Defaults
        if (empty($data['pubtypeid'])) {$data['pubtypeid'] = '';}
        if (empty($data['catfilter'])) {$data['catfilter'] = '';}
        if (empty($data['status'])) {$data['status'] = array(3, 2);}
        if (empty($data['itemlimit'])) {$data['itemlimit'] = 0;}
        if (empty($data['featuredaid'])) {$data['featuredaid'] = 0;}
        if (empty($data['alttitle'])) {$data['alttitle'] = '';}
        if (empty($data['altsummary'])) {$data['altsummary'] = '';}
        if (empty($data['showfeaturedsum'])) {$data['showfeaturedsum'] = false;}
        if (empty($data['showfeaturedbod'])) {$data['showfeaturedbod'] = false;}
        if (empty($data['moreitems'])) {$data['moreitems'] = array();}
        if (empty($data['toptype'])) {$data['toptype'] = 'date';}
        if (empty($data['showsummary'])) {$data['showsummary'] = false;}
        if (empty($data['linkpubtype'])) {$data['linkpubtype'] = false;}
        if (!isset($data['linkcat'])) {$data['linkcat'] = false;}

        if (!isset($data['showvalue'])) {
            if ($data['toptype'] == 'rating') {
                $data['showvalue'] = false;
            } else {
                $data['showvalue'] = true;
            }
        }

        $data['fields'] = array('aid', 'title');

        if (!is_array($data['status'])) {
            $statusarray = array($data['status']);
        } else {
            $statusarray = $data['status'];
        }

        if(!empty($data['catfilter'])) {
            $cidsarray = array($data['catfilter']);
        } else {
            $cidsarray = array();
        }

        // Create array based on modifications
        $article_args = array();

        // Only include pubtype if a specific pubtype is selected
        if (!empty($data['pubtypeid'])) {
            $article_args['ptid'] = $data['pubtypeid'];
        }

        // If itemlimit is set to 0, then don't pass to getall
        if ($data['itemlimit'] != 0 ) {
            $article_args['numitems'] = $data['itemlimit'];
        }

        // Add the rest of the arguments
        $article_args['cids'] = $cidsarray;
        $article_args['enddate'] = time();
        $article_args['status'] = $statusarray;
        $article_args['fields'] = $data['fields'];
        $article_args['sort'] = $data['toptype'];

        $data['filtereditems'] = xarMod::apiFunc(
            'articles', 'user', 'getall', $article_args );

        // Check for exceptions
    //    use try {} catch (Exception $e) { ... = $e->getMessage();} here
        if (!isset($data['filtereditems']))
            return; // throw back

        // Try to keep the additional headlines select list width less than 50 characters
        for ($idx = 0; $idx < count($data['filtereditems']); $idx++) {
            if (strlen($data['filtereditems'][$idx]['title']) > 50) {
                $data['filtereditems'][$idx]['title'] = substr($data['filtereditems'][$idx]['title'], 0, 47) . '...';
            }
        }

        $data['pubtypes'] = xarMod::apiFunc('articles', 'user', 'getpubtypes');
        $data['categorylist'] = xarMod::apiFunc('categories', 'user', 'getcat');
        $data['statusoptions'] = array(
            array('id' => '', 'name' => xarML('All Published')),
            array('id' => '3', 'name' => xarML('Frontpage')),
            array('id' => '2', 'name' => xarML('Approved'))
        );

        $data['sortoptions'] = array(
            array('id' => 'author', 'name' => xarML('Author')),
            array('id' => 'date', 'name' => xarML('Date')),
            array('id' => 'hits', 'name' => xarML('Hit Count')),
            array('id' => 'rating', 'name' => xarML('Rating')),
            array('id' => 'title', 'name' => xarML('Title'))
        );

        //Put together the additional featured articles list
        for($idx=0; $idx < count($data['filtereditems']); ++$idx) {
            $data['filtereditems'][$idx]['selected'] = '';
            for($mx=0; $mx < count($data['moreitems']); ++$mx) {
                if (($data['moreitems'][$mx]) == ($data['filtereditems'][$idx]['aid'])) {
                    $data['filtereditems'][$idx]['selected'] = 'selected';
                }
            }
        }
        $data['morearticles'] = $data['filtereditems'];
        //$data['blockid'] = $blockinfo['bid'];

        // Return output (template data)
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
        // Make sure we retrieve the new pubtype from the configuration form.
        // TODO: use xarVarFetch()
        xarVarFetch('pubtypeid', 'id', $vars['pubtypeid'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('catfilter', 'id', $vars['catfilter'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('status', 'int:0:4', $vars['status'], NULL, XARVAR_NOT_REQUIRED);
        xarVarFetch('itemlimit', 'int:1', $vars['itemlimit'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('toptype', 'enum:author:date:hits:rating:title', $vars['toptype'], 'date', XARVAR_NOT_REQUIRED);
        xarVarFetch('featuredaid', 'id', $vars['featuredaid'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('alttitle', 'str', $vars['alttitle'], '', XARVAR_NOT_REQUIRED);
        xarVarFetch('altsummary', 'str', $vars['altsummary'], '', XARVAR_NOT_REQUIRED);
        xarVarFetch('moreitems', 'list:id', $vars['moreitems'], NULL, XARVAR_NOT_REQUIRED);
        xarVarFetch('showfeaturedbod', 'checkbox', $vars['showfeaturedbod'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showfeaturedsum', 'checkbox', $vars['showfeaturedsum'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showsummary', 'checkbox', $vars['showsummary'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showvalue', 'checkbox', $vars['showvalue'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('linkpubtype', 'checkbox', $vars['linkpubtype'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('linkcat', 'checkbox', $vars['linkcat'], false, XARVAR_NOT_REQUIRED);

        $data['content'] = $vars;
        return $data;
    }

}
?>
